<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Función para parsear correctamente una fila INSERT del SQL
function parseSqlRow($line) {
    $line = trim($line);
    if (substr($line, -1) === ',') {
        $line = substr($line, 0, -1);
    }
    if (!preg_match('/^\((.+)\)$/', $line, $matches)) {
        return null;
    }
    $content = $matches[1];
    $values = [];
    $current = '';
    $inQuotes = false;
    $escapeNext = false;
    
    for ($i = 0; $i < strlen($content); $i++) {
        $char = $content[$i];
        
        if ($escapeNext) {
            $current .= $char;
            $escapeNext = false;
            continue;
        }
        
        if ($char === '\\') {
            $escapeNext = true;
            $current .= $char;
            continue;
        }
        
        if ($char === "'") {
            $inQuotes = !$inQuotes;
            $current .= $char;
            continue;
        }
        
        if (!$inQuotes && $char === ',') {
            $values[] = trim($current);
            $current = '';
            continue;
        }
        
        $current .= $char;
    }
    
    if ($current !== '') {
        $values[] = trim($current);
    }
    
    return $values;
}

// Función para limpiar valor (remover comillas, manejar NULL)
function cleanValue($value) {
    $value = trim($value);
    if ($value === 'NULL' || $value === '') {
        return null;
    }
    if (preg_match('/^"(.+)"$/', $value, $m)) {
        return $m[1];
    }
    if (preg_match("/^'(.+)'$/", $value, $m)) {
        return $m[1];
    }
    return $value;
}

// Categorías para SQL dump (las que existían antiguamente)
$categoriasSQL = [41, 47, 50, 57]; // Desarrollo, Soporte, Otros, Páginas Web

// Categorías para BD actual (incluye la nueva 98)
$categoriasBD = [57, 98]; // Páginas Web, 1&1 Ionos

$sqlFile = storage_path('app/crmhawki_bd_1_0.sql');
$csvFile = storage_path('app/clientes_paginas_web_' . date('Y-m-d') . '.csv');

echo "=== REGENERANDO CSV CON HOSTING Y DOMINIO ===\n";
echo "Categorías SQL dump: " . implode(', ', $categoriasSQL) . " (41=Desarrollo, 47=Soporte, 50=Otros, 57=Páginas Web)\n";
echo "Categorías BD actual: " . implode(', ', $categoriasBD) . " (57=Páginas Web, 98=1&1 Ionos)\n";
echo "Leyendo SQL dump: $sqlFile\n\n";

// 1. Leer invoice_concepts y extraer invoice_ids con las categorías relevantes
echo "1. Extrayendo invoice_concepts con category_id IN (" . implode(', ', $categoriasSQL) . ")...\n";
$invoiceConceptIds = [];
$handle = fopen($sqlFile, 'r');
$inInvoiceConcepts = false;
$columns = null;

while (($line = fgets($handle)) !== false) {
    $line = trim($line);
    
    if (preg_match('/^INSERT INTO `invoice_concepts`/', $line)) {
        $inInvoiceConcepts = true;
        // Extraer nombres de columnas
        if (preg_match('/`invoice_concepts` \((.+)\) VALUES/', $line, $matches)) {
            $colNames = preg_split('/,\s*/', $matches[1]);
            $colNames = array_map(function($c) {
                return trim($c, '`');
            }, $colNames);
            $columns = array_flip($colNames);
        }
        continue;
    }
    
    if ($inInvoiceConcepts) {
        if (preg_match('/^INSERT INTO/', $line) && !preg_match('/invoice_concepts/', $line)) {
            $inInvoiceConcepts = false;
            continue;
        }
        
        if (preg_match('/^\(/', $line)) {
            $values = parseSqlRow($line);
            if ($values && isset($columns['services_category_id'])) {
                $idx = $columns['services_category_id'];
                $catId = cleanValue($values[$idx] ?? null);
                if ($catId && in_array((int)$catId, $categoriasSQL)) {
                    $invoiceIdx = $columns['invoice_id'];
                    $invoiceId = cleanValue($values[$invoiceIdx] ?? null);
                    if ($invoiceId) {
                        $invoiceConceptIds[] = $invoiceId;
                    }
                }
            }
        }
    }
}

fclose($handle);
$invoiceConceptIds = array_unique($invoiceConceptIds);
echo "   Invoice IDs encontrados: " . count($invoiceConceptIds) . "\n\n";

// 2. Leer invoices y extraer client_id y created_at
echo "2. Extrayendo invoices con client_id y fecha...\n";
$invoicesData = [];
$handle = fopen($sqlFile, 'r');
$inInvoices = false;
$columns = null;

while (($line = fgets($handle)) !== false) {
    $line = trim($line);
    
    if (preg_match('/^INSERT INTO `invoices`/', $line)) {
        $inInvoices = true;
        if (preg_match('/`invoices` \((.+)\) VALUES/', $line, $matches)) {
            $colNames = preg_split('/,\s*/', $matches[1]);
            $colNames = array_map(function($c) {
                return trim($c, '`');
            }, $colNames);
            $columns = array_flip($colNames);
        }
        continue;
    }
    
    if ($inInvoices) {
        if (preg_match('/^INSERT INTO/', $line) && !preg_match('/invoices/', $line)) {
            $inInvoices = false;
            continue;
        }
        
        if (preg_match('/^\(/', $line)) {
            $values = parseSqlRow($line);
            if ($values && isset($columns['id'])) {
                $idIdx = $columns['id'];
                $invoiceId = cleanValue($values[$idIdx] ?? null);
                
                if (in_array($invoiceId, $invoiceConceptIds)) {
                    $clientIdx = $columns['client_id'];
                    $createdIdx = $columns['created_at'];
                    
                    $clientId = cleanValue($values[$clientIdx] ?? null);
                    $createdAt = cleanValue($values[$createdIdx] ?? null);
                    
                    if ($clientId && $createdAt) {
                        // Parsear fecha (puede tener comillas)
                        $dateStr = trim($createdAt, "'");
                        $date = \Carbon\Carbon::parse($dateStr);
                        $cutoffDate = \Carbon\Carbon::now()->subYears(3);
                        
                        if ($date->lt($cutoffDate)) {
                            if (!isset($invoicesData[$clientId]) || $date->lt(\Carbon\Carbon::parse($invoicesData[$clientId]))) {
                                $invoicesData[$clientId] = $date->format('Y-m-d');
                            }
                        }
                    }
                }
            }
        }
    }
}

fclose($handle);
echo "   Clientes únicos encontrados: " . count($invoicesData) . "\n\n";

// 3. Leer clients del SQL
echo "3. Extrayendo datos de clients...\n";
$clientsData = [];
$handle = fopen($sqlFile, 'r');
$inClients = false;
$columns = null;

while (($line = fgets($handle)) !== false) {
    $line = trim($line);
    
    if (preg_match('/^INSERT INTO `clients`/', $line)) {
        $inClients = true;
        if (preg_match('/`clients` \((.+)\) VALUES/', $line, $matches)) {
            $colNames = preg_split('/,\s*/', $matches[1]);
            $colNames = array_map(function($c) {
                return trim($c, '`');
            }, $colNames);
            $columns = array_flip($colNames);
        }
        continue;
    }
    
    if ($inClients) {
        if (preg_match('/^INSERT INTO/', $line) && !preg_match('/clients/', $line)) {
            $inClients = false;
            continue;
        }
        
        if (preg_match('/^\(/', $line)) {
            $values = parseSqlRow($line);
            if ($values && isset($columns['id'])) {
                $idIdx = $columns['id'];
                $clientId = cleanValue($values[$idIdx] ?? null);
                
                if (isset($invoicesData[$clientId])) {
                    $nameIdx = $columns['name'] ?? null;
                    $companyIdx = $columns['company'] ?? null;
                    $emailIdx = $columns['email'] ?? null;
                    $phoneIdx = $columns['phone'] ?? null;
                    $addressIdx = $columns['address'] ?? null;
                    $cityIdx = $columns['city'] ?? null;
                    $provinceIdx = $columns['province'] ?? null;
                    $zipcodeIdx = $columns['zipcode'] ?? null;
                    $webIdx = $columns['web'] ?? null;
                    $cifIdx = $columns['cif'] ?? null;
                    $primerApellidoIdx = isset($columns['primer_apellido']) ? $columns['primer_apellido'] : null;
                    $segundoApellidoIdx = isset($columns['segundo_apellido']) ? $columns['segundo_apellido'] : null;
                    
                    $clientsData[$clientId] = [
                        'id' => $clientId,
                        'name' => $nameIdx !== null ? cleanValue($values[$nameIdx] ?? null) : null,
                        'primer_apellido' => $primerApellidoIdx !== null ? cleanValue($values[$primerApellidoIdx] ?? null) : null,
                        'segundo_apellido' => $segundoApellidoIdx !== null ? cleanValue($values[$segundoApellidoIdx] ?? null) : null,
                        'company' => $companyIdx !== null ? cleanValue($values[$companyIdx] ?? null) : null,
                        'email' => $emailIdx !== null ? cleanValue($values[$emailIdx] ?? null) : null,
                        'phone' => $phoneIdx !== null ? cleanValue($values[$phoneIdx] ?? null) : null,
                        'address' => $addressIdx !== null ? cleanValue($values[$addressIdx] ?? null) : null,
                        'city' => $cityIdx !== null ? cleanValue($values[$cityIdx] ?? null) : null,
                        'province' => $provinceIdx !== null ? cleanValue($values[$provinceIdx] ?? null) : null,
                        'zipcode' => $zipcodeIdx !== null ? cleanValue($values[$zipcodeIdx] ?? null) : null,
                        'web' => $webIdx !== null ? cleanValue($values[$webIdx] ?? null) : null,
                        'cif' => $cifIdx !== null ? cleanValue($values[$cifIdx] ?? null) : null,
                        'fecha_factura' => $invoicesData[$clientId],
                    ];
                }
            }
        }
    }
}

fclose($handle);
echo "   Clientes procesados: " . count($clientsData) . "\n\n";

// 4. Leer dominios del SQL
echo "4. Extrayendo dominios...\n";
$domainsData = [];
$handle = fopen($sqlFile, 'r');
$inDominios = false;
$columns = null;

while (($line = fgets($handle)) !== false) {
    $line = trim($line);
    
    if (preg_match('/^INSERT INTO `dominios`/', $line)) {
        $inDominios = true;
        if (preg_match('/`dominios` \((.+)\) VALUES/', $line, $matches)) {
            $colNames = preg_split('/,\s*/', $matches[1]);
            $colNames = array_map(function($c) {
                return trim($c, '`');
            }, $colNames);
            $columns = array_flip($colNames);
        }
        continue;
    }
    
    if ($inDominios) {
        if (preg_match('/^INSERT INTO/', $line) && !preg_match('/dominios/', $line)) {
            $inDominios = false;
            continue;
        }
        
        if (preg_match('/^\(/', $line)) {
            $values = parseSqlRow($line);
            if ($values && isset($columns['client_id'])) {
                $clientIdx = $columns['client_id'];
                $clientId = cleanValue($values[$clientIdx] ?? null);
                
                if (isset($clientsData[$clientId])) {
                    $domainIdx = $columns['domain'] ?? ($columns['name'] ?? null);
                    if ($domainIdx !== null) {
                        $domain = cleanValue($values[$domainIdx] ?? null);
                        if ($domain) {
                            if (!isset($domainsData[$clientId])) {
                                $domainsData[$clientId] = [];
                            }
                            $domainsData[$clientId][] = $domain;
                        }
                    }
                }
            }
        }
    }
}

fclose($handle);
echo "   Clientes con dominios: " . count($domainsData) . "\n\n";

// 5. Combinar datos de la BD actual con datos del SQL
echo "5. Combinando datos de BD actual y SQL...\n";

// Obtener datos de la BD actual con las categorías 57 y 98 y facturas de hace más de 3 años
$cutoffDate = \Carbon\Carbon::now()->subYears(3);
$dbClients = \App\Models\Clients\Client::whereHas('facturas', function($query) use ($categoriasBD, $cutoffDate) {
    $query->whereHas('invoiceConcepts', function($q) use ($categoriasBD) {
        $q->whereIn('services_category_id', $categoriasBD);
    })
    ->where('created_at', '<', $cutoffDate);
})
->with(['facturas' => function($query) use ($categoriasBD, $cutoffDate) {
    $query->whereHas('invoiceConcepts', function($q) use ($categoriasBD) {
        $q->whereIn('services_category_id', $categoriasBD);
    })
    ->where('created_at', '<', $cutoffDate)
    ->orderBy('created_at', 'asc');
}, 'dominios'])
->get();

$allClients = [];

// Agregar clientes de BD actual
foreach ($dbClients as $client) {
    // Las facturas ya están cargadas y ordenadas por el eager loading
    $invoices = $client->facturas;
    
    if ($invoices->isEmpty()) {
        continue;
    }
    
    // La factura más antigua es la primera (ya ordenada)
    $oldestInvoice = $invoices->first();
    $oldestDate = \Carbon\Carbon::parse($oldestInvoice->created_at);
    
    $allClients[$client->id] = [
        'id' => $client->id,
        'name' => $client->name,
        'primer_apellido' => $client->primerApellido,
        'segundo_apellido' => $client->segundoApellido,
        'company' => $client->company,
        'email' => $client->email,
        'phone' => $client->phone,
        'address' => $client->address,
        'city' => $client->city,
        'province' => $client->province,
        'zipcode' => $client->zipcode,
        'web' => $client->web,
        'cif' => $client->cif,
        'fecha_factura' => $oldestDate->format('Y-m-d'),
        'dominios' => $client->dominios->pluck('domain')->toArray(),
    ];
}

// Agregar clientes del SQL (solo si no están ya en la BD o no están duplicados)
foreach ($clientsData as $clientId => $clientData) {
    if (!isset($allClients[$clientId])) {
        $clientData['dominios'] = $domainsData[$clientId] ?? [];
        $allClients[$clientId] = $clientData;
    }
}

echo "   Total clientes únicos: " . count($allClients) . "\n\n";

// 6. Generar CSV
echo "6. Generando CSV...\n";
$handle = fopen($csvFile, 'w');

// Encabezado
fputcsv($handle, [
    'ID',
    'Nombre',
    'Primer Apellido',
    'Segundo Apellido',
    'Empresa',
    'Email',
    'Teléfono',
    'Dirección',
    'Ciudad',
    'Provincia',
    'Código Postal',
    'Web',
    'CIF',
    'Dominios',
    'Fecha Factura Más Antigua'
], ';');

// Datos
foreach ($allClients as $client) {
    fputcsv($handle, [
        $client['id'],
        $client['name'] ?? '',
        $client['primer_apellido'] ?? '',
        $client['segundo_apellido'] ?? '',
        $client['company'] ?? '',
        $client['email'] ?? '',
        $client['phone'] ?? '',
        $client['address'] ?? '',
        $client['city'] ?? '',
        $client['province'] ?? '',
        $client['zipcode'] ?? '',
        $client['web'] ?? '',
        $client['cif'] ?? '',
        implode(', ', $client['dominios'] ?? []),
        $client['fecha_factura'] ?? '',
    ], ';');
}

fclose($handle);

echo "\n=== COMPLETADO ===\n";
echo "Archivo generado: $csvFile\n";
echo "Total de clientes: " . count($allClients) . "\n";
echo "Categorías SQL: " . implode(', ', $categoriasSQL) . "\n";
echo "Categorías BD: " . implode(', ', $categoriasBD) . "\n";



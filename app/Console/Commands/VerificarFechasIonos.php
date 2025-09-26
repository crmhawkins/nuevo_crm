<?php

namespace App\Console\Commands;

use App\Models\Dominios\Dominio;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class VerificarFechasIonos extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ionos:verificar-fechas {--domain= : Verificar solo un dominio específico}';

    /**
     * The console command description.
     */
    protected $description = 'Verificar las fechas de activación actualizadas desde IONOS';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando fechas de activación IONOS...');

        // 1. Leer el archivo JSON
        $jsonData = $this->leerArchivoJson();
        if (!$jsonData) {
            return Command::FAILURE;
        }

        $dominioEspecifico = $this->option('domain');
        $resultados = $this->verificarFechas($jsonData, $dominioEspecifico);

        $this->mostrarResultados($resultados);

        return Command::SUCCESS;
    }

    /**
     * Leer el archivo JSON
     */
    private function leerArchivoJson(): ?array
    {
        $rutaArchivo = public_path('dominios_ionos.json');
        
        if (!File::exists($rutaArchivo)) {
            $this->error("❌ El archivo dominios_ionos.json no existe en: {$rutaArchivo}");
            return null;
        }

        $contenido = File::get($rutaArchivo);
        $data = json_decode($contenido, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("❌ Error al decodificar el JSON: " . json_last_error_msg());
            return null;
        }

        return $data;
    }

    /**
     * Verificar las fechas
     */
    private function verificarFechas(array $jsonData, ?string $dominioEspecifico): array
    {
        $resultados = [
            'coinciden' => 0,
            'no_coinciden' => 0,
            'no_encontrados' => 0,
            'sin_fecha_ionos' => 0,
            'detalles' => []
        ];

        foreach ($jsonData as $dominioData) {
            $nombreDominio = $dominioData['dominio'];
            
            // Filtrar por dominio específico si se especificó
            if ($dominioEspecifico && $nombreDominio !== $dominioEspecifico) {
                continue;
            }

            // Buscar el dominio en la base de datos
            $dominio = Dominio::where('dominio', $nombreDominio)->first();

            if (!$dominio) {
                $resultados['no_encontrados']++;
                $resultados['detalles'][] = [
                    'dominio' => $nombreDominio,
                    'estado' => 'no_encontrado',
                    'mensaje' => 'Dominio no encontrado en la base de datos'
                ];
                continue;
            }

            // Verificar si tiene fecha de activación IONOS
            if (!$dominio->fecha_activacion_ionos) {
                $resultados['sin_fecha_ionos']++;
                $resultados['detalles'][] = [
                    'dominio' => $nombreDominio,
                    'estado' => 'sin_fecha_ionos',
                    'mensaje' => 'No tiene fecha de activación IONOS en la base de datos'
                ];
                continue;
            }

            // Comparar fechas
            try {
                $fechaJson = Carbon::createFromFormat('d/m/Y', $dominioData['fecha_activacion']);
                $fechaBd = $dominio->fecha_activacion_ionos;
                
                if ($fechaBd->format('Y-m-d') === $fechaJson->format('Y-m-d')) {
                    $resultados['coinciden']++;
                    $resultados['detalles'][] = [
                        'dominio' => $nombreDominio,
                        'estado' => 'coincide',
                        'fecha_json' => $fechaJson->format('Y-m-d'),
                        'fecha_bd' => $fechaBd->format('Y-m-d')
                    ];
                } else {
                    $resultados['no_coinciden']++;
                    $resultados['detalles'][] = [
                        'dominio' => $nombreDominio,
                        'estado' => 'no_coincide',
                        'fecha_json' => $fechaJson->format('Y-m-d'),
                        'fecha_bd' => $fechaBd->format('Y-m-d'),
                        'mensaje' => 'Las fechas no coinciden'
                    ];
                }
            } catch (\Exception $e) {
                $resultados['detalles'][] = [
                    'dominio' => $nombreDominio,
                    'estado' => 'error',
                    'mensaje' => "Error al procesar fecha: " . $e->getMessage()
                ];
            }
        }

        return $resultados;
    }

    /**
     * Mostrar resultados
     */
    private function mostrarResultados(array $resultados): void
    {
        $this->newLine();
        $this->info("📊 RESUMEN DE VERIFICACIÓN:");
        $this->info("   • Fechas que coinciden: {$resultados['coinciden']}");
        $this->info("   • Fechas que NO coinciden: {$resultados['no_coinciden']}");
        $this->info("   • Dominios no encontrados: {$resultados['no_encontrados']}");
        $this->info("   • Sin fecha IONOS en BD: {$resultados['sin_fecha_ionos']}");

        // Mostrar dominios que no coinciden
        if ($resultados['no_coinciden'] > 0) {
            $this->newLine();
            $this->warn("⚠️ DOMINIOS CON FECHAS QUE NO COINCIDEN:");
            $noCoinciden = array_filter($resultados['detalles'], function($item) {
                return $item['estado'] === 'no_coincide';
            });
            foreach (array_slice($noCoinciden, 0, 10) as $item) {
                $this->line("   • {$item['dominio']}: JSON({$item['fecha_json']}) vs BD({$item['fecha_bd']})");
            }
            if (count($noCoinciden) > 10) {
                $this->line("   ... y " . (count($noCoinciden) - 10) . " más");
            }
        }

        // Mostrar dominios no encontrados
        if ($resultados['no_encontrados'] > 0) {
            $this->newLine();
            $this->warn("⚠️ DOMINIOS NO ENCONTRADOS EN LA BASE DE DATOS:");
            $noEncontrados = array_filter($resultados['detalles'], function($item) {
                return $item['estado'] === 'no_encontrado';
            });
            foreach ($noEncontrados as $item) {
                $this->line("   • {$item['dominio']}");
            }
        }

        // Mostrar algunos ejemplos de coincidencias
        $coinciden = array_filter($resultados['detalles'], function($item) {
            return $item['estado'] === 'coincide';
        });

        if (!empty($coinciden)) {
            $this->newLine();
            $this->info("✅ EJEMPLOS DE FECHAS QUE COINCIDEN:");
            $ejemplos = array_slice($coinciden, 0, 5);
            foreach ($ejemplos as $item) {
                $this->line("   • {$item['dominio']}: {$item['fecha_bd']}");
            }
        }
    }
}

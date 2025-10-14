<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RealisticSeoAnalysisService;
use App\Models\Autoseo\Autoseo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class GenerateProfessionalSeoReport extends Command
{
    protected $signature = 'seo:generate-professional {id : ID del cliente Autoseo}';
    protected $description = 'Genera informe SEO profesional similar a Rankalyze con métricas calculadas';

    public function handle()
    {
        $id = $this->argument('id');

        $this->info("🔍 Generando informe SEO profesional para cliente ID: {$id}");

        try {
            // Verificar configuración
            if (!env('SERPAPI_KEY')) {
                $this->error("❌ SERPAPI_KEY no configurada en .env");
                return 1;
            }

            // Obtener cliente de la base de datos
            $autoseo = Autoseo::find($id);
            if (!$autoseo) {
                $this->error("❌ Cliente Autoseo con ID {$id} no encontrado");
                return 1;
            }

            $this->info("📊 Cliente: {$autoseo->client_name} ({$autoseo->url})");

            // Descargar datos históricos reales
            $this->info("📥 Descargando datos históricos reales...");
            $historicalData = $this->downloadRealHistoricalData($id);

            if (empty($historicalData)) {
                $this->warn("⚠️ No se encontraron datos históricos. Generando análisis inicial...");
            } else {
                $this->info("✅ Datos históricos obtenidos: " . count($historicalData) . " períodos");
            }

            // Generar análisis realista
            $this->info("🔍 Generando análisis SEO realista...");
            $analysisService = new RealisticSeoAnalysisService();
            $currentData = $analysisService->generateRealisticAnalysis($autoseo, $historicalData);

            // Calcular métricas profesionales
            $this->info("📊 Calculando métricas profesionales...");
            $professionalMetrics = $this->calculateProfessionalMetrics($currentData, $historicalData);

            // Generar informe HTML profesional
            $this->info("📝 Generando informe HTML profesional...");
            $html = $this->generateProfessionalReportHtml($currentData, $autoseo, $historicalData, $professionalMetrics);

            // Guardar informe
            $filename = "informe_seo_profesional_{$id}_" . date('Y-m-d') . ".html";
            Storage::disk('public')->put("reports/{$filename}", $html);

            $this->info("✅ Informe profesional generado exitosamente!");
            $this->info("📁 Archivo: storage/app/public/reports/{$filename}");
            $this->info("🌐 URL: " . Storage::disk('public')->url("reports/{$filename}"));

            // Mostrar resumen
            $this->displaySummary($professionalMetrics);

            // Guardar datos actuales para próximo mes
            $this->info("💾 Guardando datos actuales para próximo mes...");
            $this->storeCurrentDataForNextMonth($autoseo, $currentData);

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }

    private function downloadRealHistoricalData($id)
    {
        try {
            $this->info("   Descargando desde: https://crm.hawkins.es/api/autoseo/json/storage?id={$id}");
            
            $response = Http::timeout(120)
                ->withoutVerifying()
                ->get("https://crm.hawkins.es/api/autoseo/json/storage", ['id' => $id]);

            if (!$response->successful()) {
                $this->warn("   Error descargando datos históricos: " . $response->status());
                return [];
            }

            // Procesar ZIP
            $tempDir = storage_path("app/temp/historical_{$id}");
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            $zipPath = $tempDir . '/historical.zip';
            File::put($zipPath, $response->body());

            $zip = new \ZipArchive();
            if ($zip->open($zipPath) === TRUE) {
                $zip->extractTo($tempDir);
                $zip->close();
            } else {
                throw new \Exception('Error al extraer el ZIP');
            }

            // Leer archivos JSON
            $jsonFiles = File::glob($tempDir . '/*.json');
            $historicalData = [];

            foreach ($jsonFiles as $file) {
                $jsonContent = File::get($file);
                $data = json_decode($jsonContent, true);
                
                if ($data) {
                    // Debug: mostrar estructura del JSON
                    $this->info("   📄 Archivo: " . basename($file));
                    $this->info("   📊 Estructura: " . json_encode(array_keys($data)));
                    
                    // Normalizar estructura de datos
                    $normalizedData = $this->normalizeHistoricalData($data, $file);
                    if ($normalizedData) {
                        $historicalData[] = $normalizedData;
                    }
                } else {
                    $this->warn("   ⚠️ Error decodificando JSON: " . basename($file));
                }
            }

            // Ordenar por fecha
            usort($historicalData, function($a, $b) {
                $dateA = $a['uploaded_at'] ?? '1970-01-01';
                $dateB = $b['uploaded_at'] ?? '1970-01-01';
                return strtotime($dateA) - strtotime($dateB);
            });

            File::deleteDirectory($tempDir);
            
            $this->info("   ✅ Procesados " . count($historicalData) . " archivos históricos");
            return $historicalData;

        } catch (\Exception $e) {
            $this->warn("   ⚠️ Error procesando datos históricos: " . $e->getMessage());
            return [];
        }
    }

    private function normalizeHistoricalData($data, $filename)
    {
        // Extraer fecha del nombre del archivo si no está en los datos
        $uploadedAt = $data['uploaded_at'] ?? null;
        if (!$uploadedAt) {
            // Intentar extraer fecha del nombre del archivo
            if (preg_match('/(\d{4}-\d{2}-\d{2})/', basename($filename), $matches)) {
                $uploadedAt = $matches[1] . ' 00:00:00';
            } else {
                $uploadedAt = date('Y-m-d H:i:s', filemtime($filename));
            }
        }

        // Normalizar estructura de keywords
        $keywords = [];
        
        // Buscar keywords en diferentes estructuras posibles
        if (isset($data['detalles_keywords']) && is_array($data['detalles_keywords'])) {
            $keywords = $data['detalles_keywords'];
        } elseif (isset($data['keywords']) && is_array($data['keywords'])) {
            $keywords = $data['keywords'];
        } elseif (isset($data['results']) && is_array($data['results'])) {
            $keywords = $data['results'];
        } elseif (isset($data['organic_results']) && is_array($data['organic_results'])) {
            // Convertir organic_results a formato keywords
            foreach ($data['organic_results'] as $result) {
                if (isset($result['title']) && isset($result['position'])) {
                    $keywords[] = [
                        'keyword' => $result['title'],
                        'position' => $result['position'],
                        'url' => $result['link'] ?? '',
                        'title' => $result['title'] ?? '',
                        'snippet' => $result['snippet'] ?? ''
                    ];
                }
            }
        }

        // Si no hay keywords, intentar extraer de otros campos
        if (empty($keywords)) {
            $this->warn("   ⚠️ No se encontraron keywords en: " . basename($filename));
            return null;
        }

        return [
            'uploaded_at' => $uploadedAt,
            'detalles_keywords' => $keywords,
            'source_file' => basename($filename),
            'total_keywords' => count($keywords)
        ];
    }

    private function calculateProfessionalMetrics($currentData, $historicalData)
    {
        $this->info("   📊 Calculando métricas profesionales...");
        $this->info("   📈 Estructura currentData: " . json_encode(array_keys($currentData)));
        
        $metrics = [
            'position_average' => 0,
            'top_10_count' => 0,
            'visibility_score' => 0,
            'estimated_ctr' => 0,
            'suggested_keywords' => 0,
            'organic_traffic' => 0,
            'authority_score' => 0,
            'backlinks_estimated' => 0,
            'domain_rating' => 0,
            'position_distribution' => [],
            'result_types' => [],
            'keyword_performance' => [],
            'trends' => []
        ];

        $keywords = $currentData['detalles_keywords'] ?? [];
        $this->info("   🔑 Keywords encontradas en currentData: " . count($keywords));
        
        if (!empty($keywords)) {
            // Calcular posición promedio
            $positions = array_filter(array_column($keywords, 'position'));
            $metrics['position_average'] = !empty($positions) ? round(array_sum($positions) / count($positions), 1) : 0;
            
            // Contar keywords en top 10
            $metrics['top_10_count'] = count(array_filter($positions, fn($p) => $p <= 10));
            
            // Calcular score de visibilidad
            $foundKeywords = count(array_filter($keywords, fn($k) => $k['position'] !== null));
            $metrics['visibility_score'] = count($keywords) > 0 ? round(($foundKeywords / count($keywords)) * 100, 1) : 0;
            
            // Calcular CTR estimado
            $metrics['estimated_ctr'] = $this->calculateEstimatedCTR($positions);
            
            // Keywords sugeridas (basado en keywords no encontradas)
            $notFoundKeywords = count(array_filter($keywords, fn($k) => $k['position'] === null));
            $metrics['suggested_keywords'] = $notFoundKeywords;
            
            // Tráfico orgánico estimado
            $metrics['organic_traffic'] = $this->calculateOrganicTraffic($keywords);
            
            // Authority Score (basado en posiciones y volumen)
            $metrics['authority_score'] = $this->calculateAuthorityScore($keywords);
            
            // Backlinks estimados
            $metrics['backlinks_estimated'] = $this->calculateEstimatedBacklinks($keywords);
            
            // Domain Rating
            $metrics['domain_rating'] = $this->calculateDomainRating($keywords);
            
            // Distribución de posiciones
            $metrics['position_distribution'] = $this->calculatePositionDistribution($keywords);
            
            // Tipos de resultados
            $metrics['result_types'] = $this->calculateResultTypes($keywords);
            
            // Rendimiento por keyword
            $metrics['keyword_performance'] = $this->calculateKeywordPerformance($keywords, $historicalData);
            
            // Tendencias
            $metrics['trends'] = $this->calculateTrends($keywords, $historicalData);
        }

        return $metrics;
    }

    private function calculateEstimatedCTR($positions)
    {
        if (empty($positions)) return 0;
        
        $ctrByPosition = [
            1 => 28.5, 2 => 15.7, 3 => 11.0, 4 => 8.0, 5 => 6.1,
            6 => 4.4, 7 => 3.5, 8 => 2.8, 9 => 2.3, 10 => 1.9
        ];
        
        $totalCTR = 0;
        foreach ($positions as $position) {
            if ($position <= 10) {
                $totalCTR += $ctrByPosition[$position];
            }
        }
        
        return round($totalCTR / count($positions), 1);
    }

    private function calculateOrganicTraffic($keywords)
    {
        $totalTraffic = 0;
        
        foreach ($keywords as $keyword) {
            if ($keyword['position'] && $keyword['position'] <= 10) {
                // Estimación básica de tráfico basada en posición
                $baseTraffic = $keyword['total_results'] ? min($keyword['total_results'] / 1000, 1000) : 100;
                $positionMultiplier = max(0.1, (11 - $keyword['position']) / 10);
                $totalTraffic += $baseTraffic * $positionMultiplier;
            }
        }
        
        return round($totalTraffic);
    }

    private function calculateAuthorityScore($keywords)
    {
        $score = 0;
        $totalKeywords = count($keywords);
        
        foreach ($keywords as $keyword) {
            if ($keyword['position']) {
                if ($keyword['position'] <= 3) $score += 100;
                elseif ($keyword['position'] <= 10) $score += 70;
                elseif ($keyword['position'] <= 20) $score += 40;
                else $score += 10;
            }
        }
        
        return $totalKeywords > 0 ? round($score / $totalKeywords) : 0;
    }

    private function calculateEstimatedBacklinks($keywords)
    {
        $backlinks = 0;
        
        foreach ($keywords as $keyword) {
            if ($keyword['position'] && $keyword['position'] <= 10) {
                // Estimación basada en posición y volumen de búsqueda
                $baseBacklinks = $keyword['total_results'] ? min($keyword['total_results'] / 10000, 50) : 5;
                $positionMultiplier = max(0.1, (11 - $keyword['position']) / 10);
                $backlinks += $baseBacklinks * $positionMultiplier;
            }
        }
        
        return round($backlinks);
    }

    private function calculateDomainRating($keywords)
    {
        $rating = 0;
        $totalKeywords = count($keywords);
        
        foreach ($keywords as $keyword) {
            if ($keyword['position']) {
                if ($keyword['position'] <= 3) $rating += 100;
                elseif ($keyword['position'] <= 10) $rating += 80;
                elseif ($keyword['position'] <= 20) $rating += 60;
                elseif ($keyword['position'] <= 50) $rating += 30;
                else $rating += 10;
            }
        }
        
        return $totalKeywords > 0 ? round($rating / $totalKeywords, 1) : 0;
    }

    private function calculatePositionDistribution($keywords)
    {
        $distribution = [
            'Top 3' => 0,
            'Top 10' => 0,
            'Top 20' => 0,
            'Top 50' => 0,
            'Fuera Top 50' => 0,
            'No encontradas' => 0
        ];
        
        foreach ($keywords as $keyword) {
            $position = $keyword['position'];
            
            if ($position === null) {
                $distribution['No encontradas']++;
            } elseif ($position <= 3) {
                $distribution['Top 3']++;
            } elseif ($position <= 10) {
                $distribution['Top 10']++;
            } elseif ($position <= 20) {
                $distribution['Top 20']++;
            } elseif ($position <= 50) {
                $distribution['Top 50']++;
            } else {
                $distribution['Fuera Top 50']++;
            }
        }
        
        return $distribution;
    }

    private function calculateResultTypes($keywords)
    {
        $types = [
            'Orgánicos' => 0,
            'Pago' => 0,
            'Local' => 0,
            'Imágenes' => 0,
            'Videos' => 0
        ];
        
        // Simulación basada en keywords encontradas
        $foundKeywords = count(array_filter($keywords, fn($k) => $k['position'] !== null));
        $types['Orgánicos'] = $foundKeywords;
        $types['Pago'] = round($foundKeywords * 0.1);
        $types['Local'] = round($foundKeywords * 0.2);
        $types['Imágenes'] = round($foundKeywords * 0.15);
        $types['Videos'] = round($foundKeywords * 0.05);
        
        return $types;
    }

    private function calculateKeywordPerformance($keywords, $historicalData)
    {
        $this->info("   📊 Calculando rendimiento de keywords...");
        $this->info("   🔑 Keywords recibidas: " . count($keywords));
        
        $performance = [];
        
        foreach ($keywords as $keyword) {
            if (!isset($keyword['keyword'])) {
                $this->warn("   ⚠️ Keyword sin campo 'keyword': " . json_encode($keyword));
                continue;
            }
            
            $trend = $this->calculateKeywordTrend($keyword, $historicalData);
            $volume = $keyword['total_results'] ? min($keyword['total_results'] / 100, 1000) : 0;
            $ctr = $keyword['position'] && $keyword['position'] <= 10 ? 
                round(28.5 / $keyword['position'], 1) : 0;
            
            $performance[] = [
                'keyword' => $keyword['keyword'],
                'position' => $keyword['position'],
                'volume' => $volume,
                'trend' => $trend,
                'ctr' => $ctr
            ];
        }
        
        $this->info("   ✅ Performance calculado para " . count($performance) . " keywords");
        return $performance;
    }

    private function calculateKeywordTrend($keyword, $historicalData)
    {
        $currentPosition = $keyword['position'];
        
        if (empty($historicalData)) {
            return 'new';
        }
        
        // Buscar posición histórica más reciente
        $lastHistoricalPosition = null;
        foreach (array_reverse($historicalData) as $data) {
            if (isset($data['detalles_keywords'])) {
                foreach ($data['detalles_keywords'] as $histKeyword) {
                    if ($histKeyword['keyword'] === $keyword['keyword']) {
                        $lastHistoricalPosition = $histKeyword['position'];
                        break 2;
                    }
                }
            }
        }
        
        if ($lastHistoricalPosition === null) {
            return 'new';
        }
        
        if ($currentPosition === null && $lastHistoricalPosition === null) {
            return 'stable';
        } elseif ($currentPosition === null) {
            return 'declined';
        } elseif ($lastHistoricalPosition === null) {
            return 'improved';
        } elseif ($currentPosition < $lastHistoricalPosition) {
            return 'improved';
        } elseif ($currentPosition > $lastHistoricalPosition) {
            return 'declined';
        } else {
            return 'stable';
        }
    }

    private function calculateTrends($keywords, $historicalData)
    {
        $trends = [
            'improved' => 0,
            'declined' => 0,
            'stable' => 0,
            'new' => 0
        ];
        
        foreach ($keywords as $keyword) {
            $trend = $this->calculateKeywordTrend($keyword, $historicalData);
            $trends[$trend]++;
        }
        
        return $trends;
    }

    private function prepareKeywordEvolutionData($currentData, $historicalData)
    {
        $this->info("   🔍 Preparando datos de evolución de keywords...");
        $this->info("   📊 Datos históricos disponibles: " . count($historicalData) . " períodos");

        $evolutionData = [
            'labels' => [],
            'datasets' => [],
            'trends' => []
        ];

        // Preparar labels de meses
        $months = [];
        foreach ($historicalData as $data) {
            $date = $data['uploaded_at'] ?? 'unknown';
            $month = date('M Y', strtotime($date));
            $months[] = $month;
            $this->info("   📅 Período: " . $month . " - Keywords: " . ($data['total_keywords'] ?? 0));
        }
        $months[] = 'Actual (' . date('M Y') . ')';
        $evolutionData['labels'] = $months;

        // Recopilar todas las keywords únicas
        $allKeywords = [];
        foreach ($historicalData as $data) {
            if (isset($data['detalles_keywords']) && is_array($data['detalles_keywords'])) {
                foreach ($data['detalles_keywords'] as $keyword) {
                    if (isset($keyword['keyword'])) {
                        $allKeywords[] = $keyword['keyword'];
                    }
                }
            }
        }
        
        // Agregar keywords actuales
        if (isset($currentData['detalles_keywords']) && is_array($currentData['detalles_keywords'])) {
            foreach ($currentData['detalles_keywords'] as $keyword) {
                if (isset($keyword['keyword'])) {
                    $allKeywords[] = $keyword['keyword'];
                }
            }
        }

        $uniqueKeywords = array_unique($allKeywords);
        $this->info("   🔑 Keywords únicas encontradas: " . count($uniqueKeywords));
        
        // Limitar a las primeras 15 keywords para evitar sobrecarga visual
        $uniqueKeywords = array_slice($uniqueKeywords, 0, 15);
        
        // Colores para las líneas
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#C9CBCF', '#4BC0C0', '#FF6384', '#36A2EB',
            '#FFCE56', '#9966FF', '#FF9F40', '#C9CBCF', '#4BC0C0'
        ];

        // Crear datasets para cada keyword
        foreach ($uniqueKeywords as $index => $keyword) {
            $positions = [];
            
            // Obtener posiciones históricas
            foreach ($historicalData as $data) {
                $position = null;
                if (isset($data['detalles_keywords']) && is_array($data['detalles_keywords'])) {
                    foreach ($data['detalles_keywords'] as $kw) {
                        if (isset($kw['keyword']) && $kw['keyword'] === $keyword) {
                            $position = $kw['position'] ?? null;
                            break;
                        }
                    }
                }
                $positions[] = $position;
            }
            
            // Obtener posición actual
            $currentPosition = null;
            if (isset($currentData['detalles_keywords']) && is_array($currentData['detalles_keywords'])) {
                foreach ($currentData['detalles_keywords'] as $kw) {
                    if (isset($kw['keyword']) && $kw['keyword'] === $keyword) {
                        $currentPosition = $kw['position'] ?? null;
                        break;
                    }
                }
            }
            $positions[] = $currentPosition;

            // Solo incluir keywords que tengan al menos una posición válida
            $hasValidPosition = false;
            foreach ($positions as $pos) {
                if ($pos !== null && $pos > 0) {
                    $hasValidPosition = true;
                    break;
                }
            }

            if ($hasValidPosition) {
                // Calcular tendencia
                $trend = $this->calculateKeywordTrend(['keyword' => $keyword, 'position' => $currentPosition], $historicalData);
                $evolutionData['trends'][$keyword] = $trend;

                $evolutionData['datasets'][] = [
                    'label' => $keyword,
                    'data' => $positions,
                    'borderColor' => $colors[$index % count($colors)],
                    'backgroundColor' => $colors[$index % count($colors)] . '20',
                    'fill' => false,
                    'tension' => 0.1,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                    'pointBackgroundColor' => $colors[$index % count($colors)],
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2
                ];
            }
        }

        $this->info("   ✅ Datasets creados: " . count($evolutionData['datasets']));
        return $evolutionData;
    }

    private function prepareTopKeywordsData($keywordPerformance)
    {
        $this->info("   📊 Preparando datos de top keywords...");
        $this->info("   📈 Keywords recibidas: " . count($keywordPerformance));
        
        if (empty($keywordPerformance)) {
            $this->warn("   ⚠️ No hay datos de keywords para mostrar");
            return [
                'labels' => ['Sin datos'],
                'datasets' => [[
                    'label' => 'Posición',
                    'data' => [0],
                    'backgroundColor' => ['#6b7280'],
                    'borderColor' => ['#6b7280'],
                    'borderWidth' => 1
                ]]
            ];
        }

        // Ordenar por posición (mejores primero) y tomar top 10
        $sortedKeywords = $keywordPerformance;
        usort($sortedKeywords, function($a, $b) {
            if ($a['position'] === null && $b['position'] === null) return 0;
            if ($a['position'] === null) return 1;
            if ($b['position'] === null) return -1;
            return $a['position'] - $b['position'];
        });

        $topKeywords = array_slice($sortedKeywords, 0, 10);
        $this->info("   🏆 Top keywords seleccionadas: " . count($topKeywords));

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($topKeywords as $keyword) {
            if (!isset($keyword['keyword'])) {
                $this->warn("   ⚠️ Keyword sin campo 'keyword': " . json_encode($keyword));
                continue;
            }
            
            $labels[] = strlen($keyword['keyword']) > 20 ? 
                substr($keyword['keyword'], 0, 17) . '...' : 
                $keyword['keyword'];
            
            $position = $keyword['position'] ?: 100; // Usar 100 para no encontradas
            $data[] = $position;
            
            // Color basado en posición
            if ($keyword['position'] === null) {
                $colors[] = '#6b7280'; // Gris para no encontradas
            } elseif ($keyword['position'] <= 3) {
                $colors[] = '#10b981'; // Verde para top 3
            } elseif ($keyword['position'] <= 10) {
                $colors[] = '#3b82f6'; // Azul para top 10
            } elseif ($keyword['position'] <= 20) {
                $colors[] = '#f59e0b'; // Amarillo para top 20
            } else {
                $colors[] = '#ef4444'; // Rojo para fuera del top 20
            }
        }

        $this->info("   ✅ Datos preparados: " . count($labels) . " keywords");
        
        // Crear estructura más simple para evitar problemas de codificación
        $result = [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Posicion',
                'data' => $data,
                'backgroundColor' => $colors,
                'borderColor' => $colors,
                'borderWidth' => 1
            ]]
        ];
        
        $this->info("   🔍 Labels: " . implode(', ', $labels));
        $this->info("   🔍 Data: " . implode(', ', $data));
        $this->info("   🔍 Colors: " . implode(', ', $colors));
        
        return $result;
    }

    private function generateProfessionalReportHtml($currentData, $autoseo, $historicalData, $metrics)
    {
        $domain = parse_url($autoseo->url, PHP_URL_HOST) ?: $autoseo->url;
        
        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reporte SEO Profesional - ' . htmlspecialchars($autoseo->client_name) . '</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net/">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Figtree", sans-serif;
            background: #f8fafc;
            color: #333;
        }

        .dashboard-container {
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .logo {
            padding: 0 2rem 2rem;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 2rem;
        }

        .logo h2 {
            color: #667eea;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 1rem 2rem;
            color: #666;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #f8f9fa;
            color: #667eea;
            border-left-color: #667eea;
        }

        .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
        }

        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .chart-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .chart-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }

        .grid {
            display: grid;
        }

        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .grid-cols-5 { grid-template-columns: repeat(5, minmax(0, 1fr)); }

        .gap-6 { gap: 1.5rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mt-2 { margin-top: 0.5rem; }
        .ml-4 { margin-left: 1rem; }
        .mr-2 { margin-right: 0.5rem; }

        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-content: center; }

        .text-2xl { font-size: 1.5rem; line-height: 2rem; }
        .text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
        .text-xl { font-size: 1.25rem; line-height: 1.75rem; }
        .text-lg { font-size: 1.125rem; line-height: 1.75rem; }
        .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
        .text-xs { font-size: 0.75rem; line-height: 1rem; }

        .font-bold { font-weight: 700; }
        .font-semibold { font-weight: 600; }
        .font-medium { font-weight: 500; }

        .text-gray-900 { color: #111827; }
        .text-gray-800 { color: #1f2937; }
        .text-gray-700 { color: #374151; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-500 { color: #6b7280; }
        .text-gray-400 { color: #9ca3af; }

        .text-blue-600 { color: #2563eb; }
        .text-blue-700 { color: #1d4ed8; }
        .text-blue-800 { color: #1e40af; }
        .text-green-500 { color: #10b981; }
        .text-green-600 { color: #059669; }
        .text-green-700 { color: #047857; }
        .text-green-800 { color: #065f46; }
        .text-purple-600 { color: #9333ea; }
        .text-purple-700 { color: #7c3aed; }
        .text-purple-800 { color: #6b21a8; }
        .text-yellow-600 { color: #d97706; }
        .text-orange-600 { color: #ea580c; }
        .text-orange-700 { color: #c2410c; }
        .text-orange-800 { color: #9a3412; }

        .bg-blue-100 { background-color: #dbeafe; }
        .bg-blue-200 { background-color: #bfdbfe; }
        .bg-gray-100 { background-color: #f3f4f6; }
        .bg-gray-200 { background-color: #e5e7eb; }
        
        .hover\\:bg-blue-200:hover { background-color: #bfdbfe; }
        .hover\\:bg-gray-200:hover { background-color: #e5e7eb; }
        
        .gap-2 { gap: 0.5rem; }
        .px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
        .py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
        .text-xs { font-size: 0.75rem; line-height: 1rem; }
        .rounded-full { border-radius: 9999px; }
        .cursor-pointer { cursor: pointer; }
        .transition { transition-property: color, background-color, border-color, text-decoration-color, fill, stroke; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }
        .bg-blue-200 { background-color: #bfdbfe; }
        .bg-green-100 { background-color: #dcfce7; }
        .bg-green-200 { background-color: #bbf7d0; }
        .bg-purple-100 { background-color: #f3e8ff; }
        .bg-purple-200 { background-color: #e9d5ff; }
        .bg-yellow-100 { background-color: #fef3c7; }
        .bg-orange-100 { background-color: #fed7aa; }
        .bg-orange-200 { background-color: #fed7aa; }
        .bg-gray-50 { background-color: #f9fafb; }
        .bg-gray-100 { background-color: #f3f4f6; }
        .bg-gray-200 { background-color: #e5e7eb; }

        .bg-gradient-to-br { background-image: linear-gradient(to bottom right, var(--tw-gradient-stops)); }
        .from-green-50 { --tw-gradient-from: #f0fdf4; --tw-gradient-to: rgb(240 253 244 / 0); --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to); }
        .to-green-100 { --tw-gradient-to: #dcfce7; }
        .from-blue-50 { --tw-gradient-from: #eff6ff; --tw-gradient-to: rgb(239 246 255 / 0); --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to); }
        .to-blue-100 { --tw-gradient-to: #dbeafe; }
        .from-purple-50 { --tw-gradient-from: #faf5ff; --tw-gradient-to: rgb(250 245 255 / 0); --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to); }
        .to-purple-100 { --tw-gradient-to: #f3e8ff; }
        .from-orange-50 { --tw-gradient-from: #fff7ed; --tw-gradient-to: rgb(255 247 237 / 0); --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to); }
        .to-orange-100 { --tw-gradient-to: #ffedd5; }

        .border { border-width: 1px; }
        .border-green-200 { border-color: #bbf7d0; }
        .border-blue-200 { border-color: #bfdbfe; }
        .border-purple-200 { border-color: #e9d5ff; }
        .border-orange-200 { border-color: #fed7aa; }

        .rounded-lg { border-radius: 0.5rem; }
        .rounded-full { border-radius: 9999px; }

        .p-3 { padding: 0.75rem; }
        .p-6 { padding: 1.5rem; }
        .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
        .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
        .py-4 { padding-top: 1rem; padding-bottom: 1rem; }

        .overflow-x-auto { overflow-x: auto; }
        .min-w-full { min-width: 100%; }
        .divide-y > :not([hidden]) ~ :not([hidden]) { --tw-divide-y-reverse: 0; border-top-width: calc(1px * calc(1 - var(--tw-divide-y-reverse))); border-bottom-width: calc(1px * var(--tw-divide-y-reverse)); }
        .divide-gray-200 > :not([hidden]) ~ :not([hidden]) { --tw-divide-opacity: 1; border-color: rgb(229 231 235 / var(--tw-divide-opacity, 1)); }

        .whitespace-nowrap { white-space: nowrap; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .uppercase { text-transform: uppercase; }
        .tracking-wider { letter-spacing: 0.05em; }

        .h-\[350px\] { height: 350px; }
        .h-\[200px\] { height: 200px; }
        .h-\[400px\] { height: 400px; }

        .relative { position: relative; }
        .flex { display: flex; }

        @media (min-width: 768px) {
            .md\\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .md\\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .md\\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }

        @media (min-width: 1024px) {
            .lg\\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .lg\\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
            .lg\\:grid-cols-5 { grid-template-columns: repeat(5, minmax(0, 1fr)); }
            .lg\\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <h2><i class="fas fa-chart-line"></i> Hawkins SEO</h2>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#" class="nav-link active">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-search"></i>
                        Análisis SEO
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-bolt"></i>
                        SEO en Tiempo Real
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        Reportes
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-cog"></i>
                        Configuración
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="container mx-auto px-2 py-6 max-w-full">
                <div class="header">
                    <h1 class="text-2xl font-semibold text-gray-900">Reporte SEO Profesional</h1>
                </div>

                <!-- Header Card -->
                <div class="stat-card mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-link mr-2"></i>
                                URL: <span class="font-semibold">' . htmlspecialchars($autoseo->url) . '</span>
                            </p>
                            <p class="text-sm text-gray-600 mt-2">
                                <i class="fas fa-user mr-2"></i>
                                Cliente: <span class="font-semibold">' . htmlspecialchars($autoseo->client_name) . '</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-clock mr-2"></i>
                                Generado el:
                            </p>
                            <p class="font-semibold">' . date('d/m/Y H:i') . '</p>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-6">
                    <!-- Posición Promedio -->
                    <div class="stat-card">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100">
                                <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Posición Promedio</p>
                                <p class="text-2xl font-semibold text-gray-900">' . $metrics['position_average'] . '</p>
                            </div>
                        </div>
                    </div>

                    <!-- Top 10 -->
                    <div class="stat-card">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100">
                                <i class="fas fa-trophy text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Top 10</p>
                                <p class="text-2xl font-semibold text-gray-900">' . $metrics['top_10_count'] . '</p>
                            </div>
                        </div>
                    </div>

                    <!-- Visibilidad -->
                    <div class="stat-card">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100">
                                <i class="fas fa-eye text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Visibilidad</p>
                                <p class="text-2xl font-semibold text-gray-900">' . $metrics['visibility_score'] . '%</p>
                            </div>
                        </div>
                    </div>

                    <!-- CTR Estimado -->
                    <div class="stat-card">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100">
                                <i class="fas fa-mouse-pointer text-yellow-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">CTR Estimado</p>
                                <p class="text-2xl font-semibold text-gray-900">' . $metrics['estimated_ctr'] . '%</p>
                            </div>
                        </div>
                    </div>

                    <!-- Keywords Sugeridas -->
                    <div class="stat-card">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100">
                                <i class="fas fa-lightbulb text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Keywords Sugeridas</p>
                                <p class="text-2xl font-semibold text-gray-900">' . $metrics['suggested_keywords'] . '</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Statistics Section -->
                <div class="chart-card mb-6">
                    <div class="chart-header">
                        <h2 class="chart-title">
                            <i class="fas fa-calculator mr-2"></i>
                            Estadísticas Detalladas del Sitio Web
                        </h2>
                        <p class="text-sm text-gray-600 mt-2">Métricas calculadas basadas en el análisis SEO actual</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Tráfico Orgánico Estimado -->
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 rounded-full bg-green-200">
                                    <i class="fas fa-leaf text-green-700 text-xl"></i>
                                </div>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-medium text-green-700 mb-1">Tráfico Orgánico Mensual</p>
                                <p class="text-3xl font-bold text-green-800">' . number_format($metrics['organic_traffic']) . '</p>
                                <p class="text-xs text-green-600 mt-1">visitantes únicos</p>
                            </div>
                        </div>

                        <!-- Authority Score -->
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 rounded-full bg-blue-200">
                                    <i class="fas fa-shield-alt text-blue-700 text-xl"></i>
                                </div>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-medium text-blue-700 mb-1">Authority Score</p>
                                <p class="text-3xl font-bold text-blue-800">' . $metrics['authority_score'] . '</p>
                                <p class="text-xs text-blue-600 mt-1">de 100 puntos</p>
                            </div>
                        </div>

                        <!-- Backlinks Estimados -->
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 border border-purple-200">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 rounded-full bg-purple-200">
                                    <i class="fas fa-link text-purple-700 text-xl"></i>
                                </div>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-medium text-purple-700 mb-1">Backlinks Totales</p>
                                <p class="text-3xl font-bold text-purple-800">' . $metrics['backlinks_estimated'] . '</p>
                                <p class="text-xs text-purple-600 mt-1">enlaces entrantes</p>
                            </div>
                        </div>

                        <!-- Domain Rating -->
                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-6 border border-orange-200">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 rounded-full bg-orange-200">
                                    <i class="fas fa-star text-orange-700 text-xl"></i>
                                </div>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-medium text-orange-700 mb-1">Domain Rating</p>
                                <p class="text-3xl font-bold text-orange-800">' . $metrics['domain_rating'] . '</p>
                                <p class="text-xs text-orange-600 mt-1">de 100 puntos</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Position Distribution Chart -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h2 class="chart-title">
                                <i class="fas fa-chart-bar mr-2"></i>
                                Distribución de Posiciones
                            </h2>
                        </div>
                        <div class="h-[350px] relative flex items-center justify-center">
                            <canvas id="positionDistributionChart"></canvas>
                        </div>
                    </div>

                    <!-- Result Types Chart -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h2 class="chart-title">
                                <i class="fas fa-chart-pie mr-2"></i>
                                Tipos de Resultados
                            </h2>
                        </div>
                        <div class="h-[350px] relative flex items-center justify-center">
                            <canvas id="resultTypesChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Keyword Evolution Charts -->
                <div class="chart-card mb-6">
                    <div class="chart-header">
                        <h2 class="chart-title">
                            <i class="fas fa-chart-line mr-2"></i>
                            Evolución de Keywords en el Tiempo
                        </h2>
                        <div class="flex gap-2">
                            <button onclick="toggleKeywordVisibility(\'all\')" class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 active" id="btn-all">Todas</button>
                            <button onclick="toggleKeywordVisibility(\'top10\')" class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200" id="btn-top10">Solo Top 10</button>
                            <button onclick="toggleKeywordVisibility(\'improved\')" class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200" id="btn-improved">Solo Mejoradas</button>
                        </div>
                    </div>
                    <div class="h-[400px] relative">
                        <canvas id="keywordEvolutionChart"></canvas>
                    </div>
                </div>

                <!-- Top Keywords Performance -->
                <div class="chart-card mb-6">
                    <div class="chart-header">
                        <h2 class="chart-title">
                            <i class="fas fa-trophy mr-2"></i>
                            Rendimiento de Top Keywords
                        </h2>
                    </div>
                    <div class="h-[400px] relative">
                        <canvas id="topKeywordsChart"></canvas>
                    </div>
                </div>

                <!-- Keyword Performance Table -->
                <div class="chart-card mb-6">
                    <div class="chart-header">
                        <h2 class="chart-title">
                            <i class="fas fa-table mr-2"></i>
                            Rendimiento por Palabra Clave
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Palabra Clave
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Posición
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Volumen
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tendencia
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        CTR
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">';

        // Generar filas de la tabla
        foreach ($metrics['keyword_performance'] as $keyword) {
            $positionText = $keyword['position'] ? $keyword['position'] : 'N/A';
            $positionClass = $this->getPositionClass($keyword['position']);
            $trendIcon = $this->getTrendIcon($keyword['trend']);
            
            $html .= '<tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    ' . htmlspecialchars($keyword['keyword']) . '
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full ' . $positionClass . '">
                        ' . $positionText . '
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                    ' . number_format($keyword['volume']) . '
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center">
                        ' . $trendIcon . '
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                    ' . $keyword['ctr'] . '%
                </td>
            </tr>';
        }

        $html .= '</tbody>
                        </table>
                    </div>
                </div>

                <!-- Trends Section -->
                <div class="chart-card mb-6">
                    <div class="chart-header">
                        <h2 class="chart-title">
                            <i class="fas fa-trending-up mr-2"></i>
                            Análisis de Tendencias
                        </h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="p-4 rounded-lg bg-green-100">
                                <i class="fas fa-arrow-up text-green-600 text-2xl mb-2"></i>
                                <p class="text-sm font-medium text-green-700">Keywords Mejoradas</p>
                                <p class="text-2xl font-bold text-green-800">' . $metrics['trends']['improved'] . '</p>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="p-4 rounded-lg bg-red-100">
                                <i class="fas fa-arrow-down text-red-600 text-2xl mb-2"></i>
                                <p class="text-sm font-medium text-red-700">Keywords Empeoradas</p>
                                <p class="text-2xl font-bold text-red-800">' . $metrics['trends']['declined'] . '</p>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="p-4 rounded-lg bg-gray-100">
                                <i class="fas fa-minus text-gray-600 text-2xl mb-2"></i>
                                <p class="text-sm font-medium text-gray-700">Keywords Estables</p>
                                <p class="text-2xl font-bold text-gray-800">' . $metrics['trends']['stable'] . '</p>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="p-4 rounded-lg bg-blue-100">
                                <i class="fas fa-plus text-blue-600 text-2xl mb-2"></i>
                                <p class="text-sm font-medium text-blue-700">Keywords Nuevas</p>
                                <p class="text-2xl font-bold text-blue-800">' . $metrics['trends']['new'] . '</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Datos para las gráficas
        const positionDistribution = ' . json_encode($metrics['position_distribution']) . ';
        const resultTypes = ' . json_encode($metrics['result_types']) . ';
        const keywordEvolutionData = ' . json_encode($this->prepareKeywordEvolutionData($currentData, $historicalData)) . ';
        const topKeywordsData = ' . json_encode($this->prepareTopKeywordsData($metrics['keyword_performance'])) . ';
        
        // Fallback si topKeywordsData está vacío
        if (!topKeywordsData || Object.keys(topKeywordsData).length === 0) {
            topKeywordsData = {
                labels: ["Sin datos"],
                datasets: [{
                    label: "Posicion",
                    data: [0],
                    backgroundColor: ["#6b7280"],
                    borderColor: ["#6b7280"],
                    borderWidth: 1
                }]
            };
        }

        // Gráfica de distribución de posiciones
        const positionCtx = document.getElementById("positionDistributionChart").getContext("2d");
        new Chart(positionCtx, {
            type: "doughnut",
            data: {
                labels: Object.keys(positionDistribution),
                datasets: [{
                    data: Object.values(positionDistribution),
                    backgroundColor: [
                        "#10b981",
                        "#3b82f6",
                        "#f59e0b",
                        "#ef4444",
                        "#6b7280",
                        "#8b5cf6"
                    ],
                    borderWidth: 2,
                    borderColor: "#fff"
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "bottom"
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ": " + context.parsed + " (" + percentage + "%)";
                            }
                        }
                    }
                }
            }
        });

        // Gráfica de tipos de resultados
        const resultCtx = document.getElementById("resultTypesChart").getContext("2d");
        new Chart(resultCtx, {
            type: "pie",
            data: {
                labels: Object.keys(resultTypes),
                datasets: [{
                    data: Object.values(resultTypes),
                    backgroundColor: [
                        "#3b82f6",
                        "#ef4444",
                        "#10b981",
                        "#f59e0b",
                        "#8b5cf6"
                    ],
                    borderWidth: 2,
                    borderColor: "#fff"
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "bottom"
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ": " + context.parsed + " (" + percentage + "%)";
                            }
                        }
                    }
                }
            }
        });

        // Gráfica de evolución de keywords
        let keywordEvolutionChart;
        const evolutionCtx = document.getElementById("keywordEvolutionChart").getContext("2d");
        
        keywordEvolutionChart = new Chart(evolutionCtx, {
            type: "line",
            data: keywordEvolutionData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: "index"
                },
                plugins: {
                    legend: {
                        display: true,
                        position: "top",
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            boxWidth: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.y;
                                return context.dataset.label + ": " + (value ? "Posición " + value : "No encontrado");
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: "Período"
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: "Posición"
                        },
                        reverse: true,
                        min: 1,
                        max: 50
                    }
                }
            }
        });

        // Gráfica de top keywords
        const topKeywordsCtx = document.getElementById("topKeywordsChart").getContext("2d");
        new Chart(topKeywordsCtx, {
            type: "bar",
            data: topKeywordsData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ": Posición " + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: "Keywords"
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: "Posición"
                        },
                        reverse: true,
                        min: 1,
                        max: 20
                    }
                }
            }
        });

        // Funciones de control de gráficas
        function toggleKeywordVisibility(type) {
            const datasets = keywordEvolutionChart.data.datasets;
            
            datasets.forEach((dataset, index) => {
                let show = true;
                
                switch(type) {
                    case "top10":
                        const currentPosition = keywordEvolutionData.datasets[index] ? 
                            keywordEvolutionData.datasets[index].data[keywordEvolutionData.datasets[index].data.length - 1] : null;
                        show = currentPosition && currentPosition <= 10;
                        break;
                    case "improved":
                        const keywordName = dataset.label;
                        const trend = keywordEvolutionData.trends ? keywordEvolutionData.trends[keywordName] : null;
                        show = trend === "improved";
                        break;
                }
                
                dataset.hidden = !show;
            });
            
            keywordEvolutionChart.update();
            
            // Actualizar botones
            document.querySelectorAll(".chart-header button").forEach(btn => {
                btn.classList.remove("active", "bg-blue-100", "text-blue-700");
                btn.classList.add("bg-gray-100", "text-gray-700");
            });
            
            const activeBtn = document.getElementById("btn-" + type);
            if (activeBtn) {
                activeBtn.classList.remove("bg-gray-100", "text-gray-700");
                activeBtn.classList.add("active", "bg-blue-100", "text-blue-700");
            }
        }
    </script>
</body>
</html>';

        return $html;
    }

    private function getPositionClass($position)
    {
        if ($position === null) return 'bg-gray-100 text-gray-800';
        if ($position <= 3) return 'bg-green-100 text-green-800';
        if ($position <= 10) return 'bg-blue-100 text-blue-800';
        if ($position <= 20) return 'bg-yellow-100 text-yellow-800';
        return 'bg-red-100 text-red-800';
    }

    private function getTrendIcon($trend)
    {
        switch ($trend) {
            case 'improved':
                return '<i class="fas fa-arrow-up text-green-500"></i>';
            case 'declined':
                return '<i class="fas fa-arrow-down text-red-500"></i>';
            case 'stable':
                return '<i class="fas fa-minus text-gray-400"></i>';
            case 'new':
                return '<i class="fas fa-plus text-blue-500"></i>';
            default:
                return '<i class="fas fa-question text-gray-400"></i>';
        }
    }

    private function displaySummary($metrics)
    {
        $this->info("📊 Resumen del análisis profesional:");
        $this->info("   - Posición promedio: " . $metrics['position_average']);
        $this->info("   - Keywords en top 10: " . $metrics['top_10_count']);
        $this->info("   - Score de visibilidad: " . $metrics['visibility_score'] . "%");
        $this->info("   - CTR estimado: " . $metrics['estimated_ctr'] . "%");
        $this->info("   - Tráfico orgánico estimado: " . number_format($metrics['organic_traffic']) . " visitantes");
        $this->info("   - Authority Score: " . $metrics['authority_score'] . "/100");
        $this->info("   - Domain Rating: " . $metrics['domain_rating'] . "/100");
        $this->info("   - Keywords mejoradas: " . $metrics['trends']['improved']);
        $this->info("   - Keywords empeoradas: " . $metrics['trends']['declined']);
    }

    private function storeCurrentDataForNextMonth($autoseo, $currentData)
    {
        try {
            $filename = uniqid() . '_' . $autoseo->id . '.json';
            $relativePath = "autoseo/json/$filename";
            
            Storage::disk('public')->makeDirectory('autoseo/json');
            $saved = Storage::disk('public')->put('autoseo/json/' . $filename, json_encode($currentData, JSON_PRETTY_PRINT));

            if (!$saved) {
                $this->error("Error al guardar datos actuales");
                return;
            }

            // Actualizar json_storage en la base de datos
            $jsonStorage = $autoseo->json_storage ? json_decode($autoseo->json_storage, true) : [];
            $jsonStorage[] = [
                'id' => $autoseo->id,
                'path' => $relativePath,
                'uploaded_at' => now()->toDateTimeString(),
                'source' => 'professional_analysis'
            ];

            $autoseo->json_mesanterior = $relativePath;
            $autoseo->json_storage = json_encode($jsonStorage);
            $autoseo->save();

            $this->info("✅ Datos actuales almacenados para próximo mes");

        } catch (\Exception $e) {
            $this->error("❌ Error almacenando datos: " . $e->getMessage());
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Dominios\Dominio;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class ActualizarFechasIonos extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ionos:actualizar-fechas 
                            {--dry-run : Simular la actualización sin guardar cambios}
                            {--domain= : Actualizar solo un dominio específico}';

    /**
     * The console command description.
     */
    protected $description = 'Actualizar fechas de activación de dominios desde el archivo dominios_ionos.json';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Iniciando actualización de fechas de activación IONOS...');

        // 1. Leer el archivo JSON
        $jsonData = $this->leerArchivoJson();
        if (!$jsonData) {
            return Command::FAILURE;
        }

        $this->info("📊 Total de dominios en el archivo: " . count($jsonData));

        // 2. Procesar dominios
        $resultados = $this->procesarDominios($jsonData);

        // 3. Mostrar resultados
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

        $this->info("✅ Archivo JSON leído correctamente");
        return $data;
    }

    /**
     * Procesar los dominios
     */
    private function procesarDominios(array $jsonData): array
    {
        $resultados = [
            'actualizados' => 0,
            'no_encontrados' => 0,
            'errores' => 0,
            'errores_detalle' => [],
            'dominios_procesados' => []
        ];

        $esDryRun = $this->option('dry-run');
        $dominioEspecifico = $this->option('domain');

        if ($esDryRun) {
            $this->info("🧪 MODO DRY-RUN: No se guardarán cambios");
        }

        $progressBar = $this->output->createProgressBar(count($jsonData));
        $progressBar->start();

        foreach ($jsonData as $index => $dominioData) {
            try {
                // Filtrar por dominio específico si se especificó
                if ($dominioEspecifico && $dominioData['dominio'] !== $dominioEspecifico) {
                    $progressBar->advance();
                    continue;
                }

                $resultado = $this->procesarDominio($dominioData, $esDryRun);
                $resultados['dominios_procesados'][] = $resultado;

                if ($resultado['estado'] === 'actualizado') {
                    $resultados['actualizados']++;
                } elseif ($resultado['estado'] === 'no_encontrado') {
                    $resultados['no_encontrados']++;
                } elseif ($resultado['estado'] === 'error') {
                    $resultados['errores']++;
                    $resultados['errores_detalle'][] = $resultado;
                }

            } catch (\Exception $e) {
                $resultados['errores']++;
                $resultados['errores_detalle'][] = [
                    'dominio' => $dominioData['dominio'] ?? 'desconocido',
                    'estado' => 'error',
                    'mensaje' => "Error inesperado: " . $e->getMessage()
                ];
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        return $resultados;
    }

    /**
     * Procesar un dominio individual
     */
    private function procesarDominio(array $dominioData, bool $esDryRun): array
    {
        $nombreDominio = $dominioData['dominio'];
        $fechaActivacion = $dominioData['fecha_activacion'];

        // Buscar el dominio en la base de datos
        $dominio = Dominio::where('dominio', $nombreDominio)->first();

        if (!$dominio) {
            return [
                'dominio' => $nombreDominio,
                'estado' => 'no_encontrado',
                'mensaje' => 'Dominio no encontrado en la base de datos'
            ];
        }

        // Convertir fecha de activación
        try {
            $fechaActivacionCarbon = Carbon::createFromFormat('d/m/Y', $fechaActivacion);
        } catch (\Exception $e) {
            return [
                'dominio' => $nombreDominio,
                'estado' => 'error',
                'mensaje' => "Error al convertir fecha '{$fechaActivacion}': " . $e->getMessage()
            ];
        }

        // Verificar si la fecha ya es la misma
        if ($dominio->fecha_activacion_ionos && 
            $dominio->fecha_activacion_ionos->format('Y-m-d') === $fechaActivacionCarbon->format('Y-m-d')) {
            return [
                'dominio' => $nombreDominio,
                'estado' => 'sin_cambios',
                'mensaje' => 'La fecha de activación ya es la misma',
                'fecha_actual' => $dominio->fecha_activacion_ionos->format('Y-m-d'),
                'fecha_nueva' => $fechaActivacionCarbon->format('Y-m-d')
            ];
        }

        // Actualizar si no es dry-run
        if (!$esDryRun) {
            try {
                $dominio->fecha_activacion_ionos = $fechaActivacionCarbon;
                $dominio->save();

                return [
                    'dominio' => $nombreDominio,
                    'estado' => 'actualizado',
                    'mensaje' => 'Fecha de activación actualizada correctamente',
                    'fecha_anterior' => $dominio->fecha_activacion_ionos ? $dominio->fecha_activacion_ionos->format('Y-m-d') : 'N/A',
                    'fecha_nueva' => $fechaActivacionCarbon->format('Y-m-d')
                ];
            } catch (\Exception $e) {
                return [
                    'dominio' => $nombreDominio,
                    'estado' => 'error',
                    'mensaje' => "Error al guardar: " . $e->getMessage()
                ];
            }
        } else {
            return [
                'dominio' => $nombreDominio,
                'estado' => 'simulado',
                'mensaje' => 'Simulación: se actualizaría la fecha',
                'fecha_anterior' => $dominio->fecha_activacion_ionos ? $dominio->fecha_activacion_ionos->format('Y-m-d') : 'N/A',
                'fecha_nueva' => $fechaActivacionCarbon->format('Y-m-d')
            ];
        }
    }

    /**
     * Mostrar resultados
     */
    private function mostrarResultados(array $resultados): void
    {
        $this->newLine();
        $this->info("📊 RESUMEN DE RESULTADOS:");
        $this->info("   • Dominios actualizados: {$resultados['actualizados']}");
        $this->info("   • Dominios no encontrados: {$resultados['no_encontrados']}");
        $this->info("   • Errores: {$resultados['errores']}");

        // Mostrar dominios no encontrados
        if ($resultados['no_encontrados'] > 0) {
            $this->newLine();
            $this->warn("⚠️ DOMINIOS NO ENCONTRADOS EN LA BASE DE DATOS:");
            $noEncontrados = array_filter($resultados['dominios_procesados'], function($item) {
                return $item['estado'] === 'no_encontrado';
            });
            foreach ($noEncontrados as $item) {
                $this->line("   • {$item['dominio']}");
            }
        }

        // Mostrar errores
        if ($resultados['errores'] > 0) {
            $this->newLine();
            $this->error("❌ ERRORES ENCONTRADOS:");
            foreach ($resultados['errores_detalle'] as $error) {
                $this->line("   • {$error['dominio']}: {$error['mensaje']}");
            }
        }

        // Mostrar algunos ejemplos de actualizaciones
        $actualizados = array_filter($resultados['dominios_procesados'], function($item) {
            return in_array($item['estado'], ['actualizado', 'simulado']);
        });

        if (!empty($actualizados)) {
            $this->newLine();
            $this->info("✅ EJEMPLOS DE ACTUALIZACIONES:");
            $ejemplos = array_slice($actualizados, 0, 5);
            foreach ($ejemplos as $item) {
                $fechaAnterior = $item['fecha_anterior'] ?? 'N/A';
                $fechaNueva = $item['fecha_nueva'] ?? 'N/A';
                $this->line("   • {$item['dominio']}: {$fechaAnterior} → {$fechaNueva}");
            }
        }
    }
}

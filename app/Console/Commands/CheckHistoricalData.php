<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Autoseo\Autoseo;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CheckHistoricalData extends Command
{
    protected $signature = 'autoseo:check-historical-data {id? : ID del cliente Autoseo}';
    protected $description = 'Verifica qué datos históricos están disponibles para un cliente Autoseo';

    public function handle()
    {
        $id = $this->argument('id');
        
        if ($id) {
            $autoseo = Autoseo::find($id);
            if (!$autoseo) {
                $this->error("❌ Cliente Autoseo con ID {$id} no encontrado");
                return;
            }
            $this->checkClientData($autoseo);
        } else {
            $this->info("🔍 Verificando datos históricos de todos los clientes...");
            $clients = Autoseo::all();
            
            foreach ($clients as $client) {
                $this->line("\n" . str_repeat('=', 60));
                $this->info("Cliente ID: {$client->id} - {$client->client_name}");
                $this->checkClientData($client);
            }
        }
    }

    private function checkClientData($autoseo)
    {
        $this->line("📊 Verificando datos para: {$autoseo->client_name}");
        $this->line("URL: {$autoseo->url}");
        
        $sources = [];
        
        // 1. Verificar json_storage
        $jsonStorage = $autoseo->json_storage ? json_decode($autoseo->json_storage, true) : [];
        $this->line("\n📁 JSON Storage: " . count($jsonStorage) . " archivos");
        foreach ($jsonStorage as $index => $jsonInfo) {
            $path = $jsonInfo['path'] ?? null;
            if ($path) {
                $fullPath = storage_path('app/public/' . $path);
                $exists = File::exists($fullPath);
                $this->line("  " . ($index + 1) . ". {$path} " . ($exists ? "✅" : "❌"));
                if ($exists) {
                    $keywords = $this->countKeywordsInFile($fullPath);
                    $this->line("     Keywords: {$keywords}");
                }
            }
        }
        
        // 2. Verificar json_home
        if ($autoseo->json_home) {
            $data = json_decode($autoseo->json_home, true);
            $keywords = count($data['detalles_keywords'] ?? []);
            $this->line("\n🏠 JSON Home: ✅ {$keywords} keywords");
            $sources[] = "json_home ({$keywords} keywords)";
        } else {
            $this->line("\n🏠 JSON Home: ❌ Sin datos");
        }
        
        // 3. Verificar json_mesanterior
        if ($autoseo->json_mesanterior) {
            $data = json_decode($autoseo->json_mesanterior, true);
            $keywords = count($data['detalles_keywords'] ?? []);
            $this->line("\n📅 JSON Mes Anterior: ✅ {$keywords} keywords");
            $sources[] = "json_mesanterior ({$keywords} keywords)";
        } else {
            $this->line("\n📅 JSON Mes Anterior: ❌ Sin datos");
        }
        
        // 4. Verificar directorio específico del cliente
        $clientDir = storage_path('app/public/autoseo_json/' . $autoseo->id);
        if (File::exists($clientDir)) {
            $files = File::glob($clientDir . '/*.json');
            $this->line("\n📂 Directorio Cliente ({$clientDir}): " . count($files) . " archivos");
            foreach ($files as $file) {
                $filename = basename($file);
                $keywords = $this->countKeywordsInFile($file);
                $this->line("  - {$filename}: {$keywords} keywords");
                $sources[] = "file_{$filename} ({$keywords} keywords)";
            }
        } else {
            $this->line("\n📂 Directorio Cliente: ❌ No existe");
        }
        
        // 5. Verificar directorio general
        $generalDir = storage_path('app/public/autoseo_json');
        if (File::exists($generalDir)) {
            $files = File::glob($generalDir . '/*_' . $autoseo->id . '_*.json');
            $this->line("\n📂 Directorio General: " . count($files) . " archivos para este cliente");
            foreach ($files as $file) {
                $filename = basename($file);
                $keywords = $this->countKeywordsInFile($file);
                $this->line("  - {$filename}: {$keywords} keywords");
                $sources[] = "general_{$filename} ({$keywords} keywords)";
            }
        } else {
            $this->line("\n📂 Directorio General: ❌ No existe");
        }
        
        // Resumen
        $this->line("\n" . str_repeat('-', 40));
        $this->info("📋 RESUMEN:");
        if (empty($sources)) {
            $this->error("❌ No se encontraron datos históricos");
        } else {
            $this->info("✅ Fuentes de datos encontradas:");
            foreach ($sources as $source) {
                $this->line("  - {$source}");
            }
        }
        
        // Recomendaciones
        $this->line("\n💡 RECOMENDACIONES:");
        if (count($sources) < 2) {
            $this->warn("⚠️  Solo se encontró " . count($sources) . " fuente de datos.");
            $this->line("   Para ver evolución temporal, necesitas al menos 2 períodos de datos.");
            $this->line("   Los próximos informes mostrarán la progresión cuando haya más datos.");
        } else {
            $this->info("✅ Se encontraron múltiples fuentes de datos.");
            $this->line("   Los informes mostrarán evolución temporal completa.");
        }
    }
    
    private function countKeywordsInFile($filePath)
    {
        try {
            $content = File::get($filePath);
            $data = json_decode($content, true);
            return count($data['detalles_keywords'] ?? []);
        } catch (\Exception $e) {
            return 0;
        }
    }
}
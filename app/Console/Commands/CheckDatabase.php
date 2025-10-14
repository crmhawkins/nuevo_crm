<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckDatabase extends Command
{
    protected $signature = 'db:check';
    protected $description = 'Verifica la conexión a la base de datos y las tablas';

    public function handle()
    {
        $this->info("🔍 Verificando conexión a la base de datos...");

        try {
            // Verificar conexión
            DB::connection()->getPdo();
            $this->info("✅ Conexión a la base de datos exitosa");

            // Verificar tabla autoseo
            try {
                $count = DB::table('autoseo')->count();
                $this->info("✅ Tabla 'autoseo' encontrada con {$count} registros");

                if ($count > 0) {
                    $this->info("📊 Primeros registros:");
                    $records = DB::table('autoseo')->limit(3)->get();
                    foreach ($records as $record) {
                        $this->info("   - ID: {$record->id}, URL: {$record->url}, Email: {$record->client_email}");
                    }
                }

            } catch (\Exception $e) {
                $this->error("❌ Error con tabla 'autoseo': " . $e->getMessage());
            }

            // Verificar otras tablas importantes
            $tables = ['autoseo_reports', 'admin_user'];
            foreach ($tables as $table) {
                try {
                    $count = DB::table($table)->count();
                    $this->info("✅ Tabla '{$table}' encontrada con {$count} registros");
                } catch (\Exception $e) {
                    $this->warn("⚠️ Tabla '{$table}' no encontrada o error: " . $e->getMessage());
                }
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error de conexión: " . $e->getMessage());
            return 1;
        }
    }
}

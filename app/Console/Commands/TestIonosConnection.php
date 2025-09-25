<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\IonosApiService;

class TestIonosConnection extends Command
{
    protected $signature = 'test:ionos-connection';
    protected $description = 'Probar la conexión con la API de IONOS';

    public function handle()
    {
        $this->info('Probando conexión con la API de IONOS...');
        
        $ionosService = new IonosApiService();
        $result = $ionosService->testConnection();
        
        if ($result['success']) {
            $this->info('✅ ' . $result['message']);
            $this->newLine();
            $this->info('Configuración actual:');
            $this->line('  - API Key: ' . (config('services.ionos.api_key') ? 'Configurada' : 'No configurada'));
            $this->line('  - Base URL: ' . config('services.ionos.base_url'));
            $this->line('  - Tenant ID: ' . (config('services.ionos.tenant_id') ?: 'No configurado'));
        } else {
            $this->error('❌ ' . $result['message']);
            $this->newLine();
            $this->warn('Verifica la configuración en tu archivo .env:');
            $this->line('  IONOS_API_KEY=tu_api_key_aqui');
            $this->line('  IONOS_TENANT_ID=tu_tenant_id_aqui');
        }
    }
}

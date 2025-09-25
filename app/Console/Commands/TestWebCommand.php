<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestWebCommand extends Command
{
    protected $signature = 'test:web-command';
    protected $description = 'Comando de prueba para verificar la ejecución desde la web.';

    public function handle()
    {
        $this->info('✅ Comando ejecutado exitosamente desde la web!');
        $this->line('Fecha: ' . now()->format('Y-m-d H:i:s'));
        $this->line('Directorio: ' . base_path());
        
        return 0;
    }
}

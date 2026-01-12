<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Clients\Client;
use App\Models\Dominios\Dominio;

class GenerarTokenPagoDominio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dominio:generar-token-pago 
                            {--cliente= : ID del cliente}
                            {--email= : Email del cliente}
                            {--dominio= : ID del dominio (opcional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera un token de verificación para acceder al formulario de pago de dominio';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $clienteId = $this->option('cliente');
        $email = $this->option('email');
        $dominioId = $this->option('dominio');

        // Buscar cliente
        $cliente = null;
        if ($clienteId) {
            $cliente = Client::find($clienteId);
        } elseif ($email) {
            $cliente = Client::where('email', $email)->first();
        } else {
            // Mostrar lista de clientes
            $this->info('Debes especificar --cliente=ID o --email=EMAIL');
            $this->newLine();
            $this->info('Clientes disponibles (primeros 10):');
            $clientes = Client::take(10)->get(['id', 'name', 'email']);
            $this->table(['ID', 'Nombre', 'Email'], $clientes->map(function($c) {
                return [$c->id, $c->name, $c->email ?? 'Sin email'];
            })->toArray());
            return 1;
        }

        if (!$cliente) {
            $this->error('Cliente no encontrado');
            return 1;
        }

        // Verificar que el cliente tenga dominios
        $dominios = $cliente->dominios;
        if ($dominios->isEmpty()) {
            $this->error('El cliente no tiene dominios asociados');
            return 1;
        }

        // Si se especificó dominio, verificar que pertenezca al cliente
        $dominio = null;
        if ($dominioId) {
            $dominio = $dominios->where('id', $dominioId)->first();
            if (!$dominio) {
                $this->error('El dominio especificado no pertenece a este cliente');
                return 1;
            }
        } else {
            // Usar el primer dominio del cliente
            $dominio = $dominios->first();
        }

        // Generar token
        $token = $cliente->generarTokenVerificacion($dominio->id);

        // Obtener URL base
        $urlBase = config('app.url');
        $url = $urlBase . '/dominio/pago/' . $token;

        $this->newLine();
        $this->info('✅ Token generado exitosamente!');
        $this->newLine();
        $this->line('Cliente: ' . $cliente->name . ' (ID: ' . $cliente->id . ')');
        $this->line('Email: ' . ($cliente->email ?? 'Sin email'));
        $this->line('Dominio: ' . $dominio->dominio . ' (ID: ' . $dominio->id . ')');
        $this->newLine();
        $this->line('Token: ' . $token);
        $this->newLine();
        $this->info('🔗 URL del formulario de pago:');
        $this->line($url);
        $this->newLine();
        $this->comment('Este token expira en 30 días');
        $this->newLine();

        return 0;
    }
}

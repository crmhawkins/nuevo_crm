<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElevenlabsAgentCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'category_key',
        'category_label',
        'category_description',
        'color',
        'icon',
        'is_default',
        'order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'order' => 'integer',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(ElevenlabsAgent::class, 'agent_id', 'agent_id');
    }

    /**
     * Obtener categorías por agente
     */
    public static function getCategoriesForAgent(string $agentId): array
    {
        return static::where('agent_id', $agentId)
            ->orderBy('is_default', 'desc')
            ->orderBy('order')
            ->get()
            ->toArray();
    }

    /**
     * Crear categorías por defecto para un agente
     */
    public static function createDefaultCategories(string $agentId): void
    {
        $defaults = [
            ['key' => 'contento', 'label' => 'Contento', 'color' => '#10B981', 'description' => 'Cliente satisfecho con el servicio'],
            ['key' => 'descontento', 'label' => 'Descontento', 'color' => '#EF4444', 'description' => 'Cliente insatisfecho o molesto'],
            ['key' => 'sin_respuesta', 'label' => 'Sin Respuesta', 'color' => '#9CA3AF', 'description' => 'Cliente no responde o sin interacción'],
            ['key' => 'baja', 'label' => 'Solicitud de Baja', 'color' => '#DC2626', 'description' => 'Cliente solicita darse de baja del servicio'],
            ['key' => 'llamada_agendada', 'label' => 'Llamada Agendada', 'color' => '#3B82F6', 'description' => 'Se agendó una llamada o cita con el cliente'],
            ['key' => 'respuesta_ia', 'label' => 'Respuesta de IA/Contestador', 'color' => '#9333EA', 'description' => 'Contestó un sistema automático, asistente de voz o contestador'],
        ];

        foreach ($defaults as $index => $cat) {
            static::updateOrCreate(
                ['agent_id' => $agentId, 'category_key' => $cat['key']],
                [
                    'category_label' => $cat['label'],
                    'category_description' => $cat['description'],
                    'color' => $cat['color'],
                    'icon' => '',
                    'is_default' => true,
                    'order' => $index,
                ]
            );
        }
    }
}

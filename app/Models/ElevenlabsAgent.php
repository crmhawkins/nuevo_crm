<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ElevenlabsAgent extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'name',
        'description',
        'custom_categories',
        'archived',
        'last_call_time_unix_secs',
        'metadata',
    ];

    protected $casts = [
        'archived' => 'boolean',
        'last_call_time_unix_secs' => 'integer',
        'metadata' => 'array',
        'custom_categories' => 'array',
    ];

    /**
     * Relación con categorías personalizadas
     */
    public function categories(): HasMany
    {
        return $this->hasMany(ElevenlabsAgentCategory::class, 'agent_id', 'agent_id');
    }

    /**
     * Scope para agentes activos
     */
    public function scopeActive($query)
    {
        return $query->where('archived', false);
    }

    /**
     * Buscar agente por agent_id
     */
    public static function findByAgentId(string $agentId): ?self
    {
        return static::where('agent_id', $agentId)->first();
    }

    /**
     * Obtener nombre de agente desde caché local
     */
    public static function getNameByAgentId(string $agentId): ?string
    {
        $agent = static::findByAgentId($agentId);
        return $agent ? $agent->name : null;
    }

    /**
     * Obtener categorías del agente (personalizadas o por defecto)
     */
    public function getCategories(): array
    {
        $categories = $this->categories()->orderBy('is_default', 'desc')->orderBy('order')->get();
        
        if ($categories->isEmpty()) {
            // Crear categorías por defecto
            ElevenlabsAgentCategory::createDefaultCategories($this->agent_id);
            $categories = $this->categories()->orderBy('is_default', 'desc')->orderBy('order')->get();
        }

        return $categories->toArray();
    }
}


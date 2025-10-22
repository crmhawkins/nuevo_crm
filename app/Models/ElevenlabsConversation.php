<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Clients\Client;
use App\Models\ElevenlabsAgentCategory;
use Carbon\Carbon;

class ElevenlabsConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'agent_id',
        'agent_name',
        'client_id',
        'conversation_date',
        'duration_seconds',
        'transcript',
        'sentiment_category',
        'specific_category',
        'scheduled_call_datetime',
        'scheduled_call_notes',
        'confidence_score',
        'summary_es',
        'metadata',
        'processing_status',
        'processed_at',
        'attended',
        'attended_at',
        'attended_by',
    ];

    protected $casts = [
        'conversation_date' => 'datetime',
        'processed_at' => 'datetime',
        'attended_at' => 'datetime',
        // NO usar datetime para scheduled_call_datetime - evita conversión de timezone
        'metadata' => 'array',
        'confidence_score' => 'decimal:4',
        'duration_seconds' => 'integer',
        'attended' => 'boolean',
    ];
    
    /**
     * Accessors para scheduled_call_datetime sin conversión de timezone
     */
    public function getScheduledCallDatetimeAttribute($value)
    {
        // Devolver como Carbon sin conversión de timezone
        return $value ? \Carbon\Carbon::parse($value) : null;
    }
    
    public function setScheduledCallDatetimeAttribute($value)
    {
        // Guardar directamente sin conversión de timezone
        $this->attributes['scheduled_call_datetime'] = $value;
    }

    // Relación con el cliente
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    // Scopes
    public function scopeByCategory($query, $category)
    {
        // Buscar en ambas columnas (sentimiento o específica)
        return $query->where(function($q) use ($category) {
            $q->where('sentiment_category', $category)
              ->orWhere('specific_category', $category);
        });
    }
    
    public function scopeBySentiment($query, $sentiment)
    {
        return $query->where('sentiment_category', $sentiment);
    }
    
    public function scopeBySpecific($query, $specific)
    {
        return $query->where('specific_category', $specific);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('processing_status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('processing_status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('processing_status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('processing_status', 'failed');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('conversation_date', [$startDate, $endDate]);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('conversation_date', '>=', Carbon::now()->subDays($days));
    }

    // Accessors
    public function getDurationFormattedAttribute(): string
    {
        $duration = $this->duration_seconds ?? 0;
        $minutes = floor($duration / 60);
        $seconds = $duration % 60;
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    // Accessor para categoría de sentimiento
    public function getSentimentLabelAttribute(): string
    {
        $labels = [
            'contento' => 'Contento',
            'descontento' => 'Descontento',
            'sin_respuesta' => 'Sin Respuesta',
            'baja' => 'Solicitud de Baja',
            'llamada_agendada' => 'Llamada Agendada',
            'respuesta_ia' => 'Respuesta de IA/Contestador',
        ];
        return $labels[$this->sentiment_category] ?? 'Sin clasificar';
    }

    public function getSentimentColorAttribute(): string
    {
        $colors = [
            'contento' => '#10B981',
            'descontento' => '#EF4444',
            'sin_respuesta' => '#9CA3AF',
            'baja' => '#DC2626',
            'llamada_agendada' => '#3B82F6',
            'respuesta_ia' => '#9333EA',
        ];
        return $colors[$this->sentiment_category] ?? '#6B7280';
    }

    // Accessor para categoría específica
    public function getSpecificLabelAttribute(): string
    {
        if (!$this->specific_category || !$this->agent_id) {
            return 'Sin categoría';
        }

        $agentCategory = ElevenlabsAgentCategory::where('agent_id', $this->agent_id)
            ->where('category_key', $this->specific_category)
            ->first();
        
        return $agentCategory ? $agentCategory->category_label : ucfirst(str_replace('_', ' ', $this->specific_category));
    }

    public function getSpecificColorAttribute(): string
    {
        if (!$this->specific_category || !$this->agent_id) {
            return '#6B7280';
        }

        $agentCategory = ElevenlabsAgentCategory::where('agent_id', $this->agent_id)
            ->where('category_key', $this->specific_category)
            ->first();
        
        return $agentCategory ? $agentCategory->color : '#6B7280';
    }

    // Mantener compatibilidad con código antiguo
    public function getCategoryLabelAttribute(): string
    {
        return $this->specific_label;
    }

    public function getCategoryColorAttribute(): string
    {
        return $this->specific_color;
    }
    
    public function getCategoryColorBootstrapAttribute(): string
    {
        $colors = [
            'contento' => 'success',
            'descontento' => 'danger',
            'sin_respuesta' => 'secondary',
        ];

        return $colors[$this->sentiment_category] ?? 'secondary';
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'Pendiente',
            'processing' => 'Procesando',
            'completed' => 'Completado',
            'failed' => 'Fallido',
        ];

        return $labels[$this->processing_status] ?? 'Desconocido';
    }

    // Métodos de ayuda
    public function markAsProcessing(): void
    {
        $this->update([
            'processing_status' => 'processing',
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'processing_status' => 'completed',
            'processed_at' => now(),
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update([
            'processing_status' => 'failed',
        ]);
    }

    public function isProcessed(): bool
    {
        return $this->processing_status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->processing_status === 'pending';
    }

    // Estadísticas por categoría específica
    public static function getStatsByCategory($startDate = null, $endDate = null)
    {
        $query = static::query()->whereNotNull('specific_category');

        if ($startDate && $endDate) {
            $query->whereBetween('conversation_date', [$startDate, $endDate]);
        }

        return $query->selectRaw('specific_category as category, COUNT(*) as count')
            ->groupBy('specific_category')
            ->get();
    }
    
    // Estadísticas por sentimiento
    public static function getStatsBySentiment($startDate = null, $endDate = null)
    {
        $query = static::query()->whereNotNull('sentiment_category');

        if ($startDate && $endDate) {
            $query->whereBetween('conversation_date', [$startDate, $endDate]);
        }

        return $query->selectRaw('sentiment_category, COUNT(*) as count')
            ->groupBy('sentiment_category')
            ->get();
    }

    public static function getSatisfactionRate($startDate = null, $endDate = null): float
    {
        $query = static::query()->whereNotNull('sentiment_category');

        if ($startDate && $endDate) {
            $query->whereBetween('conversation_date', [$startDate, $endDate]);
        }

        $total = $query->count();
        if ($total === 0) {
            return 0;
        }

        $happy = (clone $query)->where('sentiment_category', 'contento')->count();
        
        return round(($happy / $total) * 100, 2);
    }

    public static function getAverageDuration($startDate = null, $endDate = null): int
    {
        $query = static::query();

        if ($startDate && $endDate) {
            $query->whereBetween('conversation_date', [$startDate, $endDate]);
        }

        return (int) $query->avg('duration_seconds');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ElevenlabsConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'client_id',
        'conversation_date',
        'duration_seconds',
        'transcript',
        'category',
        'confidence_score',
        'summary_es',
        'metadata',
        'processing_status',
        'processed_at',
    ];

    protected $casts = [
        'conversation_date' => 'datetime',
        'processed_at' => 'datetime',
        'metadata' => 'array',
        'confidence_score' => 'decimal:4',
        'duration_seconds' => 'integer',
    ];

    // Relación con el cliente (si existe)
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    // Scopes para filtros
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
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
        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function getCategoryLabelAttribute(): string
    {
        $labels = [
            'contento' => 'Contento',
            'descontento' => 'Descontento',
            'pregunta' => 'Pregunta',
            'necesita_asistencia' => 'Necesita Asistencia Extra',
            'queja' => 'Queja',
            'baja' => 'Baja',
        ];

        return $labels[$this->category] ?? 'Sin categoría';
    }

    public function getCategoryColorAttribute(): string
    {
        $colors = [
            'contento' => 'success',
            'descontento' => 'danger',
            'pregunta' => 'info',
            'necesita_asistencia' => 'warning',
            'queja' => 'danger',
            'baja' => 'dark',
        ];

        return $colors[$this->category] ?? 'secondary';
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

    // Estadísticas estáticas
    public static function getStatsByCategory($startDate = null, $endDate = null)
    {
        $query = static::query()->whereNotNull('category');

        if ($startDate && $endDate) {
            $query->whereBetween('conversation_date', [$startDate, $endDate]);
        }

        return $query->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get();
    }

    public static function getSatisfactionRate($startDate = null, $endDate = null): float
    {
        $query = static::query()->whereNotNull('category');

        if ($startDate && $endDate) {
            $query->whereBetween('conversation_date', [$startDate, $endDate]);
        }

        $total = $query->count();
        if ($total === 0) {
            return 0;
        }

        $happy = (clone $query)->where('category', 'contento')->count();
        
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

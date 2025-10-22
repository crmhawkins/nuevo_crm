<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElevenlabsSyncLog extends Model
{
    use HasFactory;

    protected $table = 'elevenlabs_sync_log';

    protected $fillable = [
        'sync_started_at',
        'sync_finished_at',
        'conversations_synced',
        'conversations_new',
        'conversations_updated',
        'status',
        'error_message',
    ];

    protected $casts = [
        'sync_started_at' => 'datetime',
        'sync_finished_at' => 'datetime',
        'conversations_synced' => 'integer',
        'conversations_new' => 'integer',
        'conversations_updated' => 'integer',
    ];

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('sync_started_at', 'desc')->limit($limit);
    }

    // Accessors
    public function getDurationAttribute(): ?int
    {
        if (!$this->sync_finished_at) {
            return null;
        }

        return $this->sync_started_at->diffInSeconds($this->sync_finished_at);
    }

    public function getDurationFormattedAttribute(): string
    {
        $duration = $this->duration;
        
        if ($duration === null) {
            return 'En progreso...';
        }

        if ($duration < 60) {
            return $duration . ' segundos';
        }

        $minutes = floor($duration / 60);
        $seconds = $duration % 60;
        return sprintf('%d min %d seg', $minutes, $seconds);
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'running' => 'En ejecución',
            'completed' => 'Completada',
            'failed' => 'Fallida',
        ];

        return $labels[$this->status] ?? 'Desconocido';
    }

    // Métodos de ayuda
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'sync_finished_at' => now(),
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'sync_finished_at' => now(),
            'error_message' => $errorMessage,
        ]);
    }

    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}

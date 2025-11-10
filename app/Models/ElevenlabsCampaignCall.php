<?php

namespace App\Models;

use App\Models\Clients\Client;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElevenlabsCampaignCall extends Model
{
    use HasFactory;

    public const STATUS_PENDIENTE = 'pendiente';
    public const STATUS_RELLAMANDO = 'rellamando';
    public const STATUS_GESTIONADA = 'gestionada';

    protected $fillable = [
        'campaign_id',
        'uid',
        'client_id',
        'phone_number',
        'status',
        'sentiment_category',
        'specific_category',
        'confidence_score',
        'summary',
        'custom_prompt',
        'metadata',
        'eleven_conversation_id',
        'eleven_conversation_internal_id',
        'managed_by',
        'managed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'managed_at' => 'datetime',
        'confidence_score' => 'decimal:4',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(ElevenlabsCampaign::class, 'campaign_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function managedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'managed_by');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ElevenlabsConversation::class, 'eleven_conversation_internal_id');
    }

    public function markAsGestionada(int $userId): void
    {
        $this->status = self::STATUS_GESTIONADA;
        $this->managed_by = $userId;
        $this->managed_at = now();
        $this->save();
    }

    public function toggleRellamando(): void
    {
        $this->status = $this->status === self::STATUS_RELLAMANDO
            ? self::STATUS_PENDIENTE
            : self::STATUS_RELLAMANDO;
        $this->save();
    }

    public function isGestionada(): bool
    {
        return $this->status === self::STATUS_GESTIONADA;
    }

    public function isRellamando(): bool
    {
        return $this->status === self::STATUS_RELLAMANDO;
    }
}


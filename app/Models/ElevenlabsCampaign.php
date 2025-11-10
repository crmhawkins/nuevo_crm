<?php

namespace App\Models;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ElevenlabsCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'name',
        'api_call_name',
        'agent_id',
        'agent_phone_number_id',
        'agent_phone_number',
        'created_by',
        'initial_prompt',
        'recipients_overview',
        'status',
        'external_batch_id',
        'total_calls',
        'completed_calls',
    ];

    protected $casts = [
        'recipients_overview' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function calls(): HasMany
    {
        return $this->hasMany(ElevenlabsCampaignCall::class, 'campaign_id');
    }

    public function scopeOwnedBy($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function incrementCompletedCalls(): void
    {
        $this->increment('completed_calls');
    }

    public function refreshCounters(): void
    {
        $this->total_calls = $this->calls()->count();
        $this->completed_calls = $this->calls()
            ->whereNotNull('eleven_conversation_internal_id')
            ->count();
        $this->save();
    }
}


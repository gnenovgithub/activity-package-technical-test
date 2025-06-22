<?php

declare(strict_types=1);

namespace Activity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Action extends Model
{
    protected $fillable = [
        'action_type',
        'subject_type',
        'subject_id',
        'user_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function performer(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function summary(): string
    {
        return __(
            'activity::messages.summary',
            [
                'user' => $this->performer->name ?? 'Unknown',
                'action' => $this->action_type,
                'subject' => class_basename($this->subject_type) . '#' . $this->subject_id
            ]
        );
    }
}

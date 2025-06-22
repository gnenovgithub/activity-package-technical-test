<?php

declare(strict_types=1);

namespace Activity;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait PerformsActions
{
    public function performedActions(): HasMany
    {
        return $this->hasMany(Action::class, 'user_id');
    }
}

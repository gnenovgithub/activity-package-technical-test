<?php

declare(strict_types=1);

namespace Activity;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

trait HasActions
{
    public static function bootHasActions(): void
    {
        static::created(function ($model) {
            // @toDo We can add ACTIONS_LOG_LEVEL or ACTIONS_CREATE_LOG_LEVEL and the metadata to be logged on higher level
            $attributes = $model->getAttributes();
            // @toDo Can be made configurable
            $exclude = ['password', 'created_at', 'updated_at', 'deleted_at', $model->getKeyName()];
            $filtered = collect($attributes)
                ->except($exclude)
                ->toArray();

            static::recordAction('create', $model, $filtered);
        });

        static::updated(function ($model) {
            static::recordAction('update', $model, $model->getChanges());
        });

        static::deleted(function ($model) {
            static::recordAction('delete', $model);
        });
    }

    public function actions(): MorphMany
    {
        return $this->morphMany(Action::class, 'subject');
    }

    protected static function recordAction(string $type, Model $model, array $metadata = []): void
    {
        $user = auth()->user();
        if (!$user) {
            return;
        }

        Action::create([
            'action_type' => $type,
            'subject_type' => get_class($model),
            'subject_id' => $model->getKey(),
            'user_id' => $user->id,
            'metadata' => $metadata
        ]);
    }
}

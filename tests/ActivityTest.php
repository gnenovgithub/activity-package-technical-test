<?php

declare(strict_types=1);

namespace Activity\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Workbench\App\Models\User;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser(): User
    {
        return User::create([
            'name' => 'Test user',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ]);
    }

    #[Test]
    public function when_a_tracked_model_is_created_an_action_is_saved(): void
    {
        $user = $this->createUser();

        $this->be($user);

        $post = $user->posts()->create([
            'title' => 'Test post',
            'body' => 'Test body',
        ]);

        static::assertCount(1, $post->actions);
        static::assertCount(1, $user->performedActions);
        static::assertSame('The model was created', $post->actions->first()->getDescription());
        static::assertTrue($post->actions->first()->is($user->performedActions->first()));
    }

    #[Test]
    public function when_a_tracked_model_is_updated_an_action_is_saved(): void
    {
        $user = $this->createUser();

        $this->be($user);

        $post = $user->posts()->create([
            'title' => 'Test post',
            'body' => 'Test body',
        ]);

        $post->title = 'Test title';
        $post->save();

        static::assertCount(2, $post->actions);
        static::assertCount(2, $user->performedActions);
        static::assertSame('The model was updated', $post->actions->last()->getDescription());
        static::assertTrue($post->actions->last()->is($user->performedActions->last()));
    }

    #[Test]
    public function when_a_tracked_model_is_deleted_an_action_is_saved(): void
    {
        $user = $this->createUser();

        $this->be($user);

        $post = $user->posts()->create([
            'title' => 'Test post',
            'body' => 'Test body',
        ]);

        $post->delete();

        static::assertCount(2, $user->performedActions);
        static::assertSame('The model was deleted', $user->performedActions->last()->getDescription());
    }
}

<?php

declare(strict_types=1);

namespace Activity\Tests;

use Activity\ActivityServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use function Orchestra\Testbench\workbench_path;

class TestCase extends OrchestraTestCase
{
    use WithWorkbench;
    use RefreshDatabase;

    protected function getPackageProviders($app)
    {
        return [ActivityServiceProvider::class];
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(
            workbench_path('database/migrations')
        );
    }
}

<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Activity\PerformsActions;
use Activity\HasActions;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * Log Actions to the DB
     */
    use PerformsActions, HasActions;

    protected $guarded = [];
}

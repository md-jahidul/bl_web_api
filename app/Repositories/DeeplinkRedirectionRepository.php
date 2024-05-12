<?php

namespace App\Repositories;

use App\Models\DeeplinkRedirection;
use App\Models\DynamicUrlRedirection;

class DeeplinkRedirectionRepository extends BaseRepository
{
    protected $modelName = DeeplinkRedirection::class;
}

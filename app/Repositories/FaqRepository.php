<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\AboutPage;
use App\Models\AlFaq;
use App\Models\Prize;

class FaqRepository extends BaseRepository
{
    public $modelName = AlFaq::class;
}

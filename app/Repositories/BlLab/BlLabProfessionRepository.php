<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories\BlLab;

use App\Models\BlLab\BlLabApplication;
use App\Models\BlLab\BlLabIndustry;
use App\Models\BlLab\BlLabProfession;
use App\Repositories\BaseRepository;

class BlLabProfessionRepository extends BaseRepository
{
    public $modelName = BlLabProfession::class;

}

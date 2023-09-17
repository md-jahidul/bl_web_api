<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories\BlLab;

use App\Models\BlLab\BlLabUser;
use App\Repositories\BaseRepository;

class BlLabsAuthenticationRepository extends BaseRepository
{
    public $modelName = BlLabUser::class;
}

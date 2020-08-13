<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\AboutPage;
use App\Models\FourGDevice;
use App\Models\Prize;

class FourGDevicesRepository extends BaseRepository
{
    public $modelName = FourGDevice::class;

    public function devices()
    {
        return $this->model
            ->where('status', 1)
            ->with('deviceTags')
            ->paginate(4);
    }
}

<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories\BlLab;

use App\Models\BlLab\BlLabApplication;
use App\Repositories\BaseRepository;

class BlLabApplicationRepository extends BaseRepository
{
    public $modelName = BlLabApplication::class;

    public function getApplications($userId)
    {
        return $this->model->where('bl_lab_user_id', $userId)
            ->select('id', 'bl_lab_user_id', 'application_id', 'submitted_at', 'application_status')
            ->with('summary')
            ->get();
    }
}

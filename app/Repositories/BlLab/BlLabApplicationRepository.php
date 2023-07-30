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

    public function getApplications($userId, $applicationStatus)
    {
        return $this->model->where('bl_lab_user_id', $userId)
            ->where('application_status', $applicationStatus)
            ->select('id', 'bl_lab_user_id', 'id_number', 'submitted_at', 'application_status')
            ->with('summary')
            ->get();
    }
}

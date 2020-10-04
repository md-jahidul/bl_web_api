<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\AboutPage;
use App\Models\CustomerFeedback;
use App\Models\Prize;

class CustomerFeedbackRepository extends BaseRepository
{
    public $modelName = CustomerFeedback::class;
}

<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\AboutPage;
use App\Models\CustomerFeedbackQuestions;
use App\Models\Prize;

class CustomerFeedbackQuesRepository extends BaseRepository
{
    public $modelName = CustomerFeedbackQuestions::class;

    public function getData()
    {
        return $this->model->where('status', 1)
            ->select('question_en', 'question_bn', 'answers_en', 'answers_bn', 'type')
            ->orderBy('sort', 'ASC')
            ->get();
    }
}

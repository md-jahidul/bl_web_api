<?php

namespace App\Services;

use App\Http\Resources\AboutPriyojonResource;
use App\Repositories\AboutPageRepository;
use App\Repositories\AboutPriyojonRepository;
use App\Repositories\CustomerFeedbackQuesRepository;
use App\Repositories\CustomerFeedbackRepository;
use App\Traits\CrudTrait;
use Illuminate\Database\QueryException;


class CustomerFeedbackService extends ApiBaseService
{
    use CrudTrait;

    /**
     * @var CustomerFeedbackRepository
     */
    private $customerFeedbackRepository;
    /**
     * @var CustomerFeedbackQuesRepository
     */
    private $customerFeedbackQuesRepo;

    /**
     * CustomerFeedbackService constructor.
     * @param CustomerFeedbackRepository $customerFeedbackRepository
     * @param CustomerFeedbackQuesRepository $customerFeedbackQuesRepository
     */
    public function __construct(
        CustomerFeedbackRepository $customerFeedbackRepository,
        CustomerFeedbackQuesRepository $customerFeedbackQuesRepository
    ) {
        $this->customerFeedbackRepository = $customerFeedbackRepository;
        $this->customerFeedbackQuesRepo = $customerFeedbackQuesRepository;
        $this->setActionRepository($customerFeedbackRepository);
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function getQuestionAns()
    {
        $questions = $this->customerFeedbackQuesRepo->getData();
        $data = [];
        foreach ($questions as $question){
            $data[] = [
              'id' => $question->id,
              'question_en' => $question->question_en,
              'question_bn' => $question->question_bn,
              'options_en' => json_decode($question->answers_en),
              'options_bn' => json_decode($question->answers_bn),
              'type' => ($question->type == 1) ? "radio" : "textarea"
            ];
        }
        return $this->sendSuccessResponse($data, 'Customer Feedback Questions');
    }

    public function feedBackSave()
    {

    }
}

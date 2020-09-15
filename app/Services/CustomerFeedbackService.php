<?php

namespace App\Services;

use App\Http\Resources\AboutPriyojonResource;
use App\Repositories\AboutPageRepository;
use App\Repositories\AboutPriyojonRepository;
use App\Repositories\CustomerFeedbackPageRepository;
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
     * @var CustomerFeedbackPageRepository
     */
    private $customerFeedbackPageRepository;

    /**
     * CustomerFeedbackService constructor.
     * @param CustomerFeedbackRepository $customerFeedbackRepository
     * @param CustomerFeedbackQuesRepository $customerFeedbackQuesRepository
     * @param CustomerFeedbackPageRepository $customerFeedbackPageRepository
     */
    public function __construct(
        CustomerFeedbackRepository $customerFeedbackRepository,
        CustomerFeedbackQuesRepository $customerFeedbackQuesRepository,
        CustomerFeedbackPageRepository $customerFeedbackPageRepository
    ) {
        $this->customerFeedbackRepository = $customerFeedbackRepository;
        $this->customerFeedbackQuesRepo = $customerFeedbackQuesRepository;
        $this->customerFeedbackPageRepository = $customerFeedbackPageRepository;
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

    public function feedBackSave($request)
    {
        $page = $this->customerFeedbackPageRepository
            ->findOneByProperties(['page_name' => $request->page_name]);

        $pageName = str_replace('_', ' ', ucfirst($request->page_name));

        if (!$page){
           $page = $this->customerFeedbackPageRepository->save(['page_name' => $pageName]);
        }

        $this->save([
            'rating' => $request->rating,
            'answers' => json_encode($request->answers),
            'page_id' => $page->id,
        ]);
        return $this->sendSuccessResponse([], 'Customer Feedback Save Successfully!');
    }
}

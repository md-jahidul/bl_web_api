<?php

namespace App\Services;

use App\Repositories\FaqRepository;

class FaqService extends ApiBaseService
{
    /**
     * @var FaqRepository
     */
    private $faqRepository;

    /**
     * AboutPageService constructor.
     * @param FaqRepository $faqRepository
     */
    public function __construct(FaqRepository $faqRepository)
    {
        $this->faqRepository = $faqRepository;
    }

    public function getQuestionAnswer($slug)
    {
        $data = $this->faqRepository
            ->findByProperties(['slug' => $slug], [
                'id', 'title', 'slug',
                'question_en', 'question_bn',
                'answer_en', 'answer_bn'
            ]);
        return $this->sendSuccessResponse($data, 'FAQ Question and Answer');
    }
}

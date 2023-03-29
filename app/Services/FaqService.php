<?php

namespace App\Services;

use App\Repositories\FaqCategoryRepository;
use App\Repositories\FaqRepository;

class FaqService extends ApiBaseService
{
    /**
     * @var FaqCategoryRepository
     */
    private $faqCatRepository;

    /**
     * AboutPageService constructor.
     * @param FaqCategoryRepository $faqCategoryRepository
     */
    public function __construct(FaqCategoryRepository $faqCategoryRepository)
    {
        $this->faqCatRepository = $faqCategoryRepository;
    }

    public function getQuestionAnswer($slug, $id)
    {
        $data = $this->faqCatRepository->getData($slug, $id);
        return $this->sendSuccessResponse($data, 'FAQ Question and Answer');
    }
}

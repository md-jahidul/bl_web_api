<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Services\AboutUsService;
use App\Services\FaqService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class FaqController extends Controller
{

    /**
     * @var FaqService
     */
    private $faqService;

    /**
     * AboutUsController constructor.
     * @param FaqService $faqService
     */
    public function __construct(FaqService $faqService)
    {
        $this->faqService = $faqService;
    }

    /**
     * @param $slug
     * @return JsonResponse|mixed
     */
    public function getFAQ($slug)
    {
        return $this->faqService->getQuestionAnswer($slug);
    }
}

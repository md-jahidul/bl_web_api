<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Resources\MixedBundleOfferResource;
use App\Models\AmarOffer;
use App\Models\FaqQuestion;
use App\Models\InternetOffer;
use App\Models\InternetPackFilter;
use App\Models\MixedBundleFilter;
use App\Models\MixedBundleOffer;
use App\Models\NearbyOffer;
use App\Http\Resources\InternetOfferResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FaqService extends ApiBaseService
{

    public function getAll()
    {
        return new FaqQuestion();
    }

    public function getQuestions()
    {
        $builder = $this->getAll()->app()->with('category');

        $questions = $builder->get()->groupBy('category.title')->sortBy('category.title')->toArray();

        ksort($questions);
        $data = [];

        foreach ($questions as $name => $question) {
            $item['category'] = $name;
            $val = [];
            foreach ($question as $q) {
                $val[] = [
                    'id' => $q['id'],
                    'question' => $q['question']
                ];
            }

            $item['questions'] = $val;
            array_push($data, $item);
        }

        return $this->sendSuccessResponse($data, 'Category wise FAQ Questions');
    }

    public function getAnswer(Request $request)
    {
        $answer = FaqQuestion::findOrFail($request->question_id);

        $data = [
            'question' => $answer->question,
            'answer' => $answer->answer
        ];

        return $this->sendSuccessResponse($data, 'FAQ Question and Answer');
    }
}

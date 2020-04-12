<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Resources\MixedBundleOfferResource;
use App\Models\AmarOffer;
use App\Models\InternetOffer;
use App\Models\InternetPackFilter;
use App\Models\MixedBundleFilter;
use App\Models\MixedBundleOffer;
use App\Models\NearbyOffer;
use App\Http\Resources\InternetOfferResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InternetPackFilterService extends ApiBaseService
{
    public function getFilters()
    {
        $filters = InternetPackFilter::with('types')->active()->get();

        $data = [];
        foreach ($filters as $filter) {
            $filter_values = json_decode($filter->filter, true);
            $data [$filter->types->slug] [] = [
                'id' => $filter->id,
                'lower' => (int)$filter_values['lower'] ?? null,
                'upper' => (int)$filter_values['upper'] ?? null,
                'unit' => $filter_values['unit'] ?? null,
            ];
        }

        return $this->sendSuccessResponse($data, 'Internet Pack Filter Options');
        ;
    }
}

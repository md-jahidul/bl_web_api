<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Resources\MixedBundleOfferResource;
use App\Models\AmarOffer;
use App\Models\InternetOffer;
use App\Models\MixedBundleFilter;
use App\Models\MixedBundleOffer;
use App\Models\NearbyOffer;
use App\Http\Resources\InternetOfferResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferService extends ApiBaseService
{

    /**
     * Retrieve internet offers
     *
     * @return string
     */
    public function internetOffer()
    {
        try {
            $data = InternetOffer::with('bonus')->get();
            $formatted_data = InternetOfferResource::collection($data);
            return $this->sendSuccessResponse($formatted_data, 'Internet offer');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    public function getMixedBundleFilters()
    {
        $filters = MixedBundleFilter::with('types')->active()->get();

        $data = [];
        foreach ($filters as $filter) {
            $filter_values = json_decode($filter->filter, true);
            if ($filter->types->slug == 'sort') {
                $data [$filter->types->slug] [] = [
                    'id' => $filter->id,
                    'name' => $filter_values['name'] ?? null
                ];
                continue;
            }
            $data [$filter->types->slug] [] = [
                'id' => $filter->id,
                'lower' => (int)$filter_values['lower'] ?? null,
                'upper' => (int)$filter_values['upper'] ?? null,
                'unit' => $filter_values['unit'] ?? null,
            ];
        }

        return $this->sendSuccessResponse($data, 'Mixed bundle offer Filter Options');
        ;
    }

    /**
     * Retrieve mixed bundle offers
     *
     * @param Request $request
     * @return string
     */
    public function mixedBundleOffer(Request $request)
    {
        try {
            $dataBuilders = new MixedBundleOffer();
            if ($request->has('price')) {
                $ids = explode(',', $request->price);
                $builders = $dataBuilders->where(function ($q) use ($ids, &$dataBuilders) {
                    foreach ($ids as $index => $id) {
                        $filters = MixedBundleFilter::find($id);
                        if (!$filters) {
                            continue;
                        }
                        $filter_options = json_decode($filters->filter, true);

                        if ($index == 0) {
                            $q->where('price', '>=', $filter_options['lower']);
                            if ($filter_options['upper']) {
                                $q->where('price', '<=', $filter_options['upper']);
                            }
                        } else {
                            $q->orWhere(function ($q) use ($filter_options) {
                                $q->where('price', '>=', $filter_options['lower']);
                                if ($filter_options['upper']) {
                                    $q->where('price', '<=', $filter_options['upper']);
                                }
                            });
                        }
                    }
                });
                $dataBuilders = $builders;
            }

            if ($request->has('minutes')) {
                $ids = explode(',', $request->minutes);
                $builders = $dataBuilders->where(function ($q) use ($ids) {
                    foreach ($ids as $index => $id) {
                        $filters = MixedBundleFilter::find($id);
                        if (!$filters) {
                            continue;
                        }
                        $filter_options = json_decode($filters->filter, true);

                        if ($index == 0) {
                            $q->where('minutes', '>=', $filter_options['lower']);
                            if ($filter_options['upper']) {
                                $q->where('minutes', '<=', $filter_options['upper']);
                            }
                        } else {
                            $q->orWhere(function ($q) use ($filter_options) {
                                $q->where('minutes', '>=', $filter_options['lower']);
                                if ($filter_options['upper']) {
                                    $q->where('minutes', '<=', $filter_options['upper']);
                                }
                            });
                        }
                    }
                });

                $dataBuilders = $builders;
            }

            if ($request->has('internet')) {
                $ids = explode(',', $request->internet);
                $builders = $dataBuilders->where(function ($q) use ($ids) {
                    foreach ($ids as $index => $id) {
                        $filters = MixedBundleFilter::find($id);
                        if (!$filters) {
                            continue;
                        }
                        $filter_options = json_decode($filters->filter, true);

                        if ($index == 0) {
                            $q->where('internet', '>=', $filter_options['lower']);
                            if ($filter_options['upper']) {
                                $q->where('internet', '<=', $filter_options['upper']);
                            }
                        } else {
                            $q->orWhere(function ($q) use ($filter_options) {
                                $q->where('internet', '>=', $filter_options['lower']);
                                if ($filter_options['upper']) {
                                    $q->where('internet', '<=', $filter_options['upper']);
                                }
                            });
                        }
                    }
                });

                $dataBuilders = $builders;
            }

            if ($request->has('sms')) {
                $ids = explode(',', $request->sms);
                $builders = $dataBuilders->where(function ($q) use ($ids) {
                    foreach ($ids as $index => $id) {
                        $filters = MixedBundleFilter::find($id);
                        if (!$filters) {
                            continue;
                        }
                        $filter_options = json_decode($filters->filter, true);

                        if ($index == 0) {
                            $q->where('sms', '>=', $filter_options['lower']);
                            if ($filter_options['upper']) {
                                $q->where('sms', '<=', $filter_options['upper']);
                            }
                        } else {
                            $q->orWhere(function ($q) use ($filter_options) {
                                $q->where('sms', '>=', $filter_options['lower']);
                                if ($filter_options['upper']) {
                                    $q->where('sms', '<=', $filter_options['upper']);
                                }
                            });
                        }
                    }
                });

                $dataBuilders = $builders;
            }

            if ($request->has('validity')) {
                $ids = explode(',', $request->validity);
                $builders = $dataBuilders->where(function ($q) use ($ids) {
                    foreach ($ids as $index => $id) {
                        $filters = MixedBundleFilter::find($id);
                        if (!$filters) {
                            continue;
                        }
                        $filter_options = json_decode($filters->filter, true);

                        if ($index == 0) {
                            $q->where('validity', '>=', $filter_options['lower']);
                            if ($filter_options['upper']) {
                                $q->where('validity', '<=', $filter_options['upper']);
                            }
                        } else {
                            $q->orWhere(function ($q) use ($filter_options) {
                                $q->where('validity', '>=', $filter_options['lower']);
                                if ($filter_options['upper']) {
                                    $q->where('validity', '<=', $filter_options['upper']);
                                }
                            });
                        }
                    }
                });

                $dataBuilders = $builders;
            }

            if ($request->has('sort')) {
                $ids = explode(',', $request->sort);
                foreach ($ids as $index => $id) {
                    $filters = MixedBundleFilter::find($id);
                    if (!$filters) {
                        continue;
                    }
                    $filter_options = json_decode($filters->filter, true);

                    if ($filter_options['value'] == 'popular') {
                        $dataBuilders->orderBy('points', 'DESC');
                    }
                    if ($filter_options['value'] == 'price_low_high') {
                        $dataBuilders->orderBy('price', 'ASC');
                    }
                    if ($filter_options['value'] == 'price_high_low') {
                        $dataBuilders->orderBy('price', 'DESC');
                    }
                }
            }

            $offers = $dataBuilders->get();
            $formatted_data = MixedBundleOfferResource::collection($offers);


            return $this->sendSuccessResponse($formatted_data, 'Mixed bundle offer');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }


    /**
     * Buy Mixed bundle  offers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function buyMixedBundleOffer($id)
    {

        try {
            if ($id == 2) {
                return $this->sendErrorResponse(
                    'Insufficient Balance',
                    [],
                    HttpStatusCode::BAD_REQUEST
                );
            }

            $data = ['id' => $id];
            return $this->sendSuccessResponse($data, 'Mixed Bundle offer');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }


    /**
     * Retrieve amar offer offers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAmarOffer()
    {
        try {
            $data = AmarOffer::select('id', 'internet', 'sms', 'minutes', 'validity', 'price', 'tag')->get();
            return $this->sendSuccessResponse($data, 'Buy Amar offer');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }


    /**
     * Buy amar offer offers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function buyAmarOffer($id)
    {
        try {
            if ($id == 2) {
                return $this->sendErrorResponse(
                    'Insufficient Balance',
                    [],
                    HttpStatusCode::BAD_REQUEST
                );
            }

            $data = ['id' => $id];
            return $this->sendSuccessResponse($data, 'Amar offer');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }


    /**
     * Retrieve nearby offers
     *
     * @param $lat
     * @param $long
     * @return string
     */
    public function nearbyOffer($lat, $long)
    {
        try {
            $data['location'] = "Gulshan 1, Dahaka";
            $data['nearby_offer'] = NearbyOffer::select('vendor', 'type', 'validity', 'offer', 'image')->get();

            return $this->sendSuccessResponse($data, 'Nearby offer');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }
}

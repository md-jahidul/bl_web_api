<?php

namespace App\Services\Banglalink;

use App\Services\ApiBaseService;
use Illuminate\Http\JsonResponse;

class RechargeIrisOfferService extends BaseService
{
    protected $responseFormatter;
    protected const RECHARGE_IRIS_OFFER_ENDPOINT = "/product-offer/iris-offers/v1/get-digital-offer";
    protected const HEADER = [
        "accept: application/json"
    ];

    /**
     * RechargeIrisOfferService constructor.
     */
    public function __construct()
    {
        $this->responseFormatter = new ApiBaseService();
    }

    private function prepareIrisAPI($msisdn)
    {
//        $channel = "MobileApp";
        $channel = env("IRIS_OFFER_CHANNEL_NAME", "MobileApp");
        $amount = 29;
        return self::RECHARGE_IRIS_OFFER_ENDPOINT . '?amount=' . $amount . '&channel=' . $channel . '&msisdn=88' . $msisdn;
    }

    /**
     * @param $format
     * @return JsonResponse
     */
    public function getIrisOffer($msisdn)
    {
        $response = $this->get($this->prepareIrisAPI($msisdn), [],self::HEADER);
//        dd($response, $this->prepareIrisAPI($msisdn));
        $irisOffers = json_decode($response['response'], true);

        if (isset($response['status_code']) && $response['status_code'] != 200){
            return $this->responseFormatter->sendErrorResponse('API hub internal server error',
                [
                    'message' => 'Currently Service Unavailable. Please,try again later',
                ], $response['status_code']
            );
        }

        $data = [];
        foreach ($irisOffers as $offer) {
            if (isset($offer['dataProduct']) || isset($offer['voiceMin']) || isset($offer['voiceRate'])) {
                $rateCutVol = 0;
                $rateCutUnitEn = null;
                $rateCutUnitBn = null;
                if (isset($offer['voiceRate'])){
                    list($rateCutVol, $rateCutUnitEn, $rateCutUnitBn) = $this->prepareCallRateData($offer['voiceRate']);
                }

                $bonusVolume = 0;
                $bonusVolumeType = null;
                if(isset($offer['extra'][0])){
                    $bonusInfo = $offer['extra'][0];
                    $bonusVolume = $bonusInfo['productVolume'];
                    $bonusVolumeType = $bonusInfo['productType'];
                }

                $dataVolume = isset($offer['dataProduct']) && is_numeric($offer['dataProduct']) ? $offer['dataProduct'] : 0;

                $data[] = [
                    'offer_id' => $offer['id'] ?? null,
                    'transaction_id' => $offer['transactionId'] ?? null,
                    'name' => $offer['name'] ?? null,
                    'price' => $offer['rechargeAmount'] ?? null,
                    'data_volume' => isset($offer['dataVolumeType']) ? (($offer['dataVolumeType'] == "GB") ? $dataVolume * 1024 : $offer['dataProduct']) : null,
                    'bonus_volume' => isset($bonusVolumeType) ? (($bonusVolumeType == "GB") ? $bonusVolume * 1024 : $bonusVolume) : 0,
                    'minutes' => $offer['voiceMin'] ?? null,
                    'sms' => $offer['sms'] ?? null,
                    'call_rate' => $rateCutVol ?? null,
                    'call_rate_unit' => $rateCutUnitEn ?? null,
                    'call_rate_unit_bn' => $rateCutUnitBn ?? null,
                    'validity' => $offer['validity'] ?? null,
                    'validity_unit' => ($offer['validity'] > 1) ? "Days" : "Day",
                ];
            }
        }
        return $this->responseFormatter->sendSuccessResponse($data, 'Recharge Iris Offer List');
    }

    public function prepareCallRateData($callRateValue)
    {
        $rateCutVol = 0;
        $rateCutUnitEn = null;
        $rateCutUnitBn = null;

        if (strstr($callRateValue,'p/sec')) {
            $valUnit = explode(' ', str_replace("p/sec", ' p/sec',$callRateValue));
            $rateCutVol = $valUnit[0];
            $rateCutUnitEn = "Paisa/Sec";
            $rateCutUnitBn = "পয়সা/সেকেন্ড";
        } elseif (strstr($callRateValue,'tk/min')) {
            $valUnit = explode(' ', str_replace("tk/min", ' tk/min',$callRateValue));
            $rateCutVol = $valUnit[0];
            $rateCutUnitEn = "Tk/Min";
            $rateCutUnitBn = "টাকা/মিনিট";
        } elseif (strstr($callRateValue,'p/min')) {
            $valUnit = explode(' ', str_replace("p/min", ' p/min',$callRateValue));
            $rateCutVol = $valUnit[0];
            $rateCutUnitEn = "Paisa/Min";
            $rateCutUnitBn = "পয়সা/মিনিট";
        }
        return [
            $rateCutVol,
            $rateCutUnitEn,
            $rateCutUnitBn
        ];
    }
}

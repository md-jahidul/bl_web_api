<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 12/27/19
 * Time: 12:00 PM
 */

namespace App\Services;


use App\Enums\HttpStatusCode;

class SslCommerzService extends ApiBaseService
{
    /**
     * @var NumberValidationService
     */
    protected $numberValidationService;

    /**
     * SslCommerzService constructor.
     * @param NumberValidationService $numberValidationService
     */
    public function __construct(NumberValidationService $numberValidationService)
    {
        $this->numberValidationService = $numberValidationService;
    }


    public function validateMobiles($mobiles)
    {
        $type = null;
        $mobileList = explode(',', $mobiles);

        foreach ($mobileList as $mobile) {
            $validationResponse = $this->numberValidationService->validateNumberWithResponse(substr($mobile, 2));

            if ($validationResponse->getData()->status == 'FAIL') {
                return $validationResponse;
            }
            $data = $validationResponse->getData()->data;
            if ($type) {
                if ($data->connectionType != $type) {
                    return $this->sendErrorResponse($type. ' and '. $data->connectionType. ' recharge cannot be done at same request',
                        [], HttpStatusCode::VALIDATION_ERROR);
                }
            } else {
                $type = $data->connectionType;
            }
        }

        return $this->sendSuccessResponse(['mobiles' => $mobiles, 'connectionType' => $type], '');
    }
}

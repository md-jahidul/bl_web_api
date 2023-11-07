<?php
/**
 * Title:
 * User: Mehedi Hasan Shuvo
 * Date: 05/03/2023
 * Description: Inspired from MyBLCashBack
 */

namespace App\Services;


use App\Enums\HttpStatusCode;
use App\Repositories\AlCashBackRepository;
use Illuminate\Http\JsonResponse;

class AlCashBackService extends ApiBaseService
{

    /**
     * @var AlCashBackRepository
     */
    private $cashBackRepository;

    /**
     * AlCashBackService constructor.
     * @param AlCashBackRepository $cashBackRepository
     */
    public function __construct(AlCashBackRepository $cashBackRepository)
    {
        $this->cashBackRepository = $cashBackRepository;
    }

    public function getCashbackamount($request)
    {
        $cashbackDetails = [];
        foreach ($request->recharge_amounts as $amount){
            $cashBackData = $this->cashBackRepository->getCashBackAmount($amount['amount']);
            if ($cashBackData) {
                $cashbackDetails[] =  $cashBackData;
            }
        }
        return $this->sendSuccessResponse(['cashback_details' => $cashbackDetails], 'Cashback Details');
    }

}

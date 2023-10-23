<?php

/**
 * Title:
 * User: Mehedi Hasan Shuvo
 * Date: 05/03/2023
 * Description: Inspired from MyBLCashBack
 */

namespace App\Repositories;

use App\Models\AlCashBack;
use Carbon\Carbon;

class AlCashBackRepository extends BaseRepository
{
    public $modelName = AlCashBack::class;

    public function getCashBackAmount($rechargeAmount)
    {
        $cashbackData =  $this->model
            ->select('id')
            ->where('status', 1)
            ->startEndDate()
            ->WhereHas('cashBackProducts', function($q) use($rechargeAmount) {
                $q->where('status', 1);
                $q->where('recharge_amount', $rechargeAmount);
            })
            ->with(['cashBackProducts' => function($q) use($rechargeAmount) {
                $q->select('al_cash_back_id', 'recharge_amount', 'cash_back_amount', 'end_date');
                $q->where('status', 1);
                $q->where('recharge_amount', $rechargeAmount)
                ->startEndDate();
            }])
            ->get();

        $cashbackDetails = [];
        if(count($cashbackData)) {
            $cashbackProductArray = [];
            foreach ($cashbackData as $data) {
                foreach ($data->cashBackProducts as $value){
                    $row = [];
                    $row['al_cash_back_id'] = $value->al_cash_back_id;
                    $row['recharge_amount'] = $value->recharge_amount;
                    $row['cash_back_amount'] = $value->cash_back_amount;
                    $row['end_date'] = $value->end_date;
                    $cashbackProductArray[$value->recharge_amount][] = $row;
                }
                // foreach ($data->cashBackProducts as $value){
                //     $cashbackAmount = $value->cash_back_amount ?? 0;
                // }

            }

            $selectedArray = $cashbackProductArray[$rechargeAmount];
            $final_key = 0;
            $final_end_date = '';

            /**
             * Title:
             * User: Mehedi Hasan Shuvo
             * Date: 05/03/2023
             * Description: Will select product with closest end_date
             *
             */
            foreach ($selectedArray as $key => $single) {
                if ($final_end_date == '') {
                    $final_key = $key;
                    $final_end_date = $single['end_date'];
                }
                if ($single['end_date'] < $final_end_date) {
                    $final_end_date = $single['end_date'];
                    $final_key = $key;
                }
            }

            $cashbackDetails['recharge_amount'] = $selectedArray[$final_key]['recharge_amount'];
            $cashbackDetails['cashback_amount'] = $selectedArray[$final_key]['cash_back_amount'];

        }
        return $cashbackDetails;
    }
}

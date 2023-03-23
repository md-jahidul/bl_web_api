<?php

namespace App\Repositories;


use App\Models\RechargeLog;

/**
 * Class BannerProductPurchaseDetailRepository
 * @package App\Repositories
 */
class RechargeLogRepository extends BaseRepository
{
    protected $modelName = RechargeLog::class;

    public function findOneByTrxId($trxId)
    {
        return $this->model::where('trx_id', $trxId)->first();
    }

    public function updateData($data, $trxId){

        return $this->model::where('trx_id', $trxId)->update($data);
    }
}

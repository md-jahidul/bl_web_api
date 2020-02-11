<?php


namespace App\Repositories;


use App\Models\AmarOfferDetails;

class AmarOfferDetailsRepository extends BaseRepository
{
    public $modelName = AmarOfferDetails::class;

    /**
     * @param $type
     * @return mixed
     */
    public function offerDetails($type)
    {
        return $this->model->where('type', $type)
            ->select('details_en', 'details_bn', 'type')
            ->first();
    }
}

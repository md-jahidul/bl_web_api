<?php

namespace App\Repositories;

use App\Models\CurrentBalance;

/**
 * Class CurrentBalanceRepository
 * @package App\Repositories
 */
class CurrentBalanceRepository
{

    /**
     * @var CurrentBalance
     */
    protected $model;


    /**
     * CurrentBalanceRepository constructor.
     * @param CurrentBalance $model
     */
    public function __construct(CurrentBalance $model)
    {
        $this->model = $model;
    }


    /**
     * @return mixed
     */
    public function getCurrentBalance()
    {
        return $this->model->get();
    }
}

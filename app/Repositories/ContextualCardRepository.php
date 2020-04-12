<?php

namespace App\Repositories;

use App\Models\ContextualCard;

/**
 * Class BannerRepository
 * @package App\Repositories
 */
class ContextualCardRepository
{

    /**
     * @var ContextualCard
     */
    protected $model;


    /**
     * BannerRepository constructor.
     * @param ContextualCard $model
     */
    public function __construct(ContextualCard $model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve Contextual Card info
     *
     * @return mixed
     */
    public function getContextualCardInfo()
    {
        return $this->model->get();
    }
}

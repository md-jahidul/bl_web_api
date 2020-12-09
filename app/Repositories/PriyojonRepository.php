<?php

namespace App\Repositories;

use App\Models\AboutUsBanglalink;
use App\Models\Priyojon;

/**
 * Class AboutUsRepository
 * @package App\Repositories
 */
class PriyojonRepository extends BaseRepository
{
    protected $modelName = Priyojon::class;

    public function getMenuForSlug($alias)
    {
        return $this->model->where('alias', $alias)->first();
    }
}

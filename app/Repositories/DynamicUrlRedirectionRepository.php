<?php

namespace App\Repositories;

use App\Models\DynamicUrlRedirection;

class DynamicUrlRedirectionRepository extends BaseRepository
{
    protected $modelName = DynamicUrlRedirection::class;

    public function getRedirections()
    {
        return $this->model->select('id', 'from_url', 'to_url')->where('status', 1)->groupby('from_url')->get();
    }
}

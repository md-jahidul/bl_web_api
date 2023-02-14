<?php

namespace App\Repositories;

use App\Models\OtherDynamicPage;
use Illuminate\Support\Facades\Auth;

// use App\Repositories\BaseRepository;


class ExploreCDetailsRepository extends BaseRepository
{
    protected $modelName = OtherDynamicPage::class;

    public function findOneBySlug($slug)
    {
        return $this->model->where('type', 'explore_c')
                            ->where('url_slug', $slug)
                            ->orWhere('url_slug_bn', $slug)
                            ->select(
                                'id','page_name_en','page_name_bn',
                                'page_header','page_header_bn','schema_markup',
                                'url_slug','url_slug_bn','created_by','updated_by'
                            )
                            ->first();
    }

}

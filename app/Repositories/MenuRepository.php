<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\AboutPage;
use App\Models\Menu;
use App\Models\Prize;

class MenuRepository extends BaseRepository
{
    public $modelName = Menu::class;

    public function headerMenus()
    {
        return $this->model->where('parent_id', 0)
            ->where('status', 1)
            ->with(['children' => function($query){
                $query->where('status', 1);
            }])
            ->orderBy('display_order')
            ->get();
    }
}
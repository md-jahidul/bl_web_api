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
            ->select(
                'id',
                'en_label_text',
                'bn_label_text',
                'code as key',
                'url',
                'url_bn',
                'external_site'
            )
            ->with(['children' => function($query){
                $query->where('status', 1)
                ->select(
                    'id', 'parent_id', 'en_label_text', 'bn_label_text', 'code as key', 'url', 'url_bn', 'external_site',
                    'description_en', 'description_bn', 'icon'
                );
            }])
            ->orderBy('display_order')
            ->get();
    }
}

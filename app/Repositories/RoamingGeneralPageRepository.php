<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 24/02/2020
 */

namespace App\Repositories;

use App\Models\RoamingGeneralPage;
use App\Models\RoamingGeneralPageComponents;

class RoamingGeneralPageRepository extends BaseRepository {

    public $modelName = RoamingGeneralPage::class;

    public function roamingGeneralPage($pageSlug) {
        $page = $this->model
                        ->where('page_type', $pageSlug)->first();
        $data = [];
        
        $data['category_slug'] = $page->page_type;
        $data['title_en'] = $page->title_en;
        $data['title_bn'] = $page->title_bn;
        
        $count = 0;
        
        $components = RoamingGeneralPageComponents::where('parent_id', $page->id)->orderBy('position')->get();

        foreach ($components as $v) {
            $data['components'][$count]['id'] = $v->id;
            $data['components'][$count]['body_text_en'] = $v->body_text_en;
            $data['components'][$count]['body_text_bn'] = $v->body_text_bn;
            $data['components'][$count]['big_font'] = $v->big_font;
            $data['components'][$count]['payment_block'] = $v->payment_block;
            $data['components'][$count]['component_type'] = $v->component_type;
            $count++;
        }
        return $data;
    }

}

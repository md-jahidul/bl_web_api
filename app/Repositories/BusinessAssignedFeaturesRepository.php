<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 11/02/2020
 */

namespace App\Repositories;

use App\Models\BusinessAssignedFeatures;

class BusinessAssignedFeaturesRepository extends BaseRepository {

    public $modelName = BusinessAssignedFeatures::class;

    

    public function getAssignedFeatures($packageId, $parentType) {
        $data = $this->model->select('bf.id as feature_id', 'bf.icon_url', 'bf.alt_text', 'bf.title', 'bf.title_bn')
                ->leftJoin("business_features as bf", "bf.id", "=", "business_assigned_features.feature_id")
                ->where(array('business_assigned_features.parent_type' => $parentType, 'business_assigned_features.parent_id' => $packageId))
                ->orderBy('bf.sort')->get();
        
        $features = [];
        $count = 0;
        foreach ($data as $f) {
            $features[$count]['feature_id'] = $f->feature_id;
            $features[$count]['icon'] = config('filesystems.image_host_url') . $f->icon_url;
            $features[$count]['alt_text'] = $f->alt_text;
            $features[$count]['title'] = $f->title;
            $features[$count]['title_bn'] = $f->title_bn;
            $count++;
        }
        return $features;
    }

   

}

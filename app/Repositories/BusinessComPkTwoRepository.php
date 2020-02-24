<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 11/02/2020
 */

namespace App\Repositories;

use App\Models\BusinessComPackageTwo;
use DB;

class BusinessComPkTwoRepository extends BaseRepository {

    public $modelName = BusinessComPackageTwo::class;

    public function saveComponent($position, $title, $name, $data, $days, $price, $srvsId, $oldComponents) {
        $insertData = [];
        foreach ($title as $k => $v) {
            $insertData[] = array(
                'title' => $v,
                'package_name' => $name[$k],
                'data_limit' => $data[$k],
                'package_days' => $days[$k],
                'price' => $price[$k],
                'position' => $position + $oldComponents,
                'service_id' => $srvsId,
            );
        }

        $this->model->insert($insertData);
    }

    public function getComponent($serviceId) {
        $component = $this->model->select(DB::raw('group_concat(package_name) name, position'))
                        ->where('service_id', $serviceId)->groupBy('position')->get();
        return $component;
    }

    public function deleteComponent($serviceId, $position) {
        $component = $this->model->where(array('service_id' => $serviceId, 'position' => $position))->delete();
        return $component;
    }

    public function changePosition($serviceId, $newPosition, $oldPosition) {
        $component = $this->model->where(array('service_id' => $serviceId, 'position' => $oldPosition))
                ->update(array('position' => $newPosition));
        return $component;
    }

}

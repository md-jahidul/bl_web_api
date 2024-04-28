<?php

namespace App\Repositories\Page;

use App\Models\Page\NewPageComponentData;
use App\Repositories\BaseRepository;
use App\Traits\FileTrait;

class PgComponentDataRepository extends BaseRepository
{
    public $modelName = NewPageComponentData::class;
    use FileTrait;

    public function createOrUpdate($componentData)
    {
        $componentDataObj = $this->model->find($componentData['id']);
        if ($componentDataObj) {
            if ($componentDataObj->value_en != $componentData['value_en'])
                $this->deleteFile($componentDataObj->value_en);
            $componentDataObj->update($componentData);
            return $componentDataObj;
        }
        return $this->model->create($componentData);
    }

    public function deleteComponentData($id)
    {
        $this->model->whereIn('component_id', [$id])->delete();
    }
}

<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 24/06/2020
 */

namespace App\Repositories;

use App\Models\EthicsInfo;
use App\Models\EthicsFiles;

class EthicsRepository extends BaseRepository {

    public $modelName = EthicsInfo::class;

    public function getPageInfo() {
        return $this->model->first();
    }

    public function getFiles() {
        return EthicsFiles::where('status', 1)->where('file_path', '!=', NULL)
                        ->orderBy('sort')->get();
    }

}

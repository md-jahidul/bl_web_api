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
        $info = $this->model->first();

        return $info;
    }

    public function getFiles() {
        $files = EthicsFiles::where('status', 1)->where('file_path', '!=', NULL)
                        ->orderBy('sort')->get();

        return $files;
    }

}

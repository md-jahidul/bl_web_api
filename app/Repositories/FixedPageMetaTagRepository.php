<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\AboutPage;
use App\Models\MetaTag;
use App\Models\Prize;

class FixedPageMetaTagRepository extends BaseRepository
{
    public $modelName = MetaTag::class;
}

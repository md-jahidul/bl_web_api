<?php

namespace App\Repositories\Page;

use App\Models\Page\NewPage;
use App\Repositories\BaseRepository;

//use App\Repositories\BaseRepository;

class PageRepository extends BaseRepository
{
    public $modelName = NewPage::class;
}

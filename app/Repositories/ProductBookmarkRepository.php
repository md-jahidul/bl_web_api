<?php
namespace App\Repositories;

use App\Models\AlProductBookmark;
use App\Models\PartnerOffer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductBookmarkRepository extends BaseRepository
{
    /**
     * @var string
     */
    public $modelName = AlProductBookmark::class;
}

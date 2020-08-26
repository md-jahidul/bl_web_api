<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 26-Aug-19
 * Time: 4:31 PM
 */

namespace App\Services;

use App\Repositories\FourGDeviceTagRepository;
use App\Traits\CrudTrait;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class FourGDeviceTagService
{
    use CrudTrait;

    /**
     * @var FourGDeviceTagRepository
     */
    private $fourGDeviceTagRepository;

    /**
     * TagCategoryService constructor.
     * @param FourGDeviceTagRepository $fourGDeviceTagRepository
     */
    public function __construct(FourGDeviceTagRepository $fourGDeviceTagRepository)
    {
        $this->fourGDeviceTagRepository = $fourGDeviceTagRepository;
    }

}

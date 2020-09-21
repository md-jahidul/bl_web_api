<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:23
 */

namespace App\Services;

use App\Repositories\BanglalinkThreeGRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BanglalinkThreeGService extends ApiBaseService
{
    use CrudTrait;
    use FileTrait;

    /**
     * @var BanglalinkThreeGRepository
     */
    private $banglalinkThreeGRepository;


    /**
     * DigitalServicesService constructor.
     * @param BanglalinkThreeGRepository $banglalinkThreeGRepository
     */
    public function __construct(
        BanglalinkThreeGRepository $banglalinkThreeGRepository
    ) {
        $this->banglalinkThreeGRepository = $banglalinkThreeGRepository;
        $this->setActionRepository($banglalinkThreeGRepository);
    }

    public function threeGdata()
    {
        $bannerImage = $this->banglalinkThreeGRepository->findOneByProperties(['type' => 'banner_image']);
        $data = [
            'body_section' => $this->banglalinkThreeGRepository->findWithoutBanner(),
            'banner' => $bannerImage['other_attributes']
        ];
        return $this->sendSuccessResponse($data, '3G Info With Banner Image');
    }
}

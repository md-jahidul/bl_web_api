<?php


namespace App\Services;


use App\Http\Resources\AppServiceResource;
use App\Repositories\AppServiceBookmarkRepository;
use App\Repositories\AppServiceCategoryRepository;
use App\Repositories\AppServiceProductRepository;
use App\Repositories\AppServiceTabRepository;
use App\Repositories\ReferralCodeRepository;
use App\Traits\CrudTrait;
use Illuminate\Http\JsonResponse;

class ReferralCodeService extends ApiBaseService
{
    use CrudTrait;

    /**
     * @var ReferralCodeRepository
     */
    private $referralCodeRepository;

    /**
     * Referral Code constructor.
     * @param ReferralCodeRepository $referralCodeRepository
     */
    public function __construct(
        ReferralCodeRepository $referralCodeRepository
    ) {
        $this->referralCodeRepository = $referralCodeRepository;
        $this->setActionRepository($referralCodeRepository);
    }

    public function referralCodeGenerator($mobileNo)
    {
        $letter = chr(rand(65,90));
        dd($letter);
//        return $this->sendSuccessResponse($data, 'Referral Code');
    }

}

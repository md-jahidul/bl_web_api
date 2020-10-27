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
use Illuminate\Support\Str;

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
        $referralCode = Str::random(12);
        $existCode = $this->referralCodeRepository->findOneByProperties(['mobile_no' => $mobileNo]);

        if (!$existCode) {
            $refCodeStore['mobile_no'] = $mobileNo;
            $refCodeStore['referral_code'] = $referralCode;
            $this->referralCodeRepository->findOneByProperties(['referral_code' => $referralCode]);
            $nowCode = $this->save($refCodeStore);
        }

        $data['referral_code'] = isset($existCode->referral_code) ? $existCode->referral_code : $nowCode->referral_code;
        return $this->sendSuccessResponse($data, 'Referral Code');
    }

}

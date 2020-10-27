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

    public function referralCodeGenerator($mobileNo, $appId)
    {
        $existCode = $this->referralCodeRepository->findOneByProperties(['mobile_no' => $mobileNo]);

        if (!$existCode) {
            $this->codeStore($mobileNo, $appId);
        }

        $multipleCode =  $this->referralCodeRepository->findByProperties(['mobile_no' => $mobileNo], ['app_id']);
        if ($multipleCode) {
            $multiAppId = [];
            foreach ($multipleCode as $value) {
                $multiAppId[] = $value->app_id;
            }
        }

        if (isset($multiAppId) && !in_array($appId, $multiAppId)) {
            $this->codeStore($mobileNo, $appId);
        }

        $data['referral_code'] = $this->referralCodeRepository->findOneByProperties(['mobile_no' => $mobileNo, 'app_id' => $appId])->referral_code;
        return $this->sendSuccessResponse($data, 'Referral Code');
    }

    public function codeStore($mobileNo, $appId)
    {
        $referralCode = Str::random(12);
        $refCodeStore['mobile_no'] = $mobileNo;
        $refCodeStore['referral_code'] = $referralCode;
        $refCodeStore['app_id'] = $appId;
        return $this->save($refCodeStore);
    }

    public function shareReferralCount($data)
    {
        $referredUser = $this->referralCodeRepository->findOneByProperties(['mobile_no' => $data['mobile_no'], 'app_id' => $data['app_id']]);
        if ($referredUser) {
            $countShare['share_count'] = $referredUser['share_count']+1;
            $referredUser->update($countShare);
            return $this->sendSuccessResponse([], 'Referral code shared successfully!!');
        }
        return $this->sendErrorResponse('App info not found');
    }

}

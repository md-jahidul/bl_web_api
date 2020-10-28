<?php


namespace App\Services;


use App\Http\Resources\AppServiceResource;
use App\Repositories\AppServiceBookmarkRepository;
use App\Repositories\AppServiceCategoryRepository;
use App\Repositories\AppServiceProductRepository;
use App\Repositories\AppServiceTabRepository;
use App\Repositories\ReferralCodeRepository;
use App\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
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

    /**
     * @param $mobileNo
     * @param $appId
     * @return JsonResponse|mixed
     */
    public function referralCodeGenerator($mobileNo, $appId)
    {
        $existCode = $this->referralCodeRepository->findOneByProperties(['mobile_no' => $mobileNo, 'app_id' => $appId]);
        $referralCode = Str::random(12);
        if (!$existCode) {
            $refCodeStore['mobile_no'] = $mobileNo;
            $refCodeStore['app_id'] = $appId;
            $refCode = $this->codeStore($referralCode);
            $refCodeStore['referral_code'] = $refCode;
            $this->save($refCodeStore);
        }

        $data = $this->referralCodeRepository->findOneByProperties(['mobile_no' => $mobileNo, 'app_id' => $appId], ['referral_code']);
        return $this->sendSuccessResponse($data, 'Referral Code');
    }

    /**
     * @param $referralCode
     * @param $mobileNo
     * @param $appId
     * @return Model
     */
    public function codeStore($referralCode)
    {
        $existingCode = $this->referralCodeRepository->findOneByProperties(['referral_code' => $referralCode]);
        if (isset($existingCode->referral_code)) {
            if ($existingCode->referral_code == $referralCode) {
                $uniqueRefCode = Str::random(12);
                $referralCode = $this->codeStore($uniqueRefCode);
            }
        }
        return $referralCode;
    }

    /**
     * @param $data
     * @return JsonResponse|mixed
     */
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

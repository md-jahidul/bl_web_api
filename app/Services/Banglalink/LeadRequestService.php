<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\API\V1\ConfigController;
use App\Mail\LeadFoundMail;
use App\Repositories\LeadCategoryRepository;
use App\Repositories\LeadProductRepository;
use App\Repositories\LeadRequestRepository;
use App\Services\ApiBaseService;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

/**
 * Class FnfService
 * @package App\Services\Banglalink
 */
class LeadRequestService extends ApiBaseService
{
    use CrudTrait;
    use FileTrait;

    const BUSINESS = 'business';
    const BUSINESS_SUB = 'business';
    const BUSINESS_REQUEST_TYPE = 'business';

    const POSTPAID_CAT = ['postpaid'];
    const POSTPAID_SUB = ['package'];
    const POSTPAID_REQUEST_TYPE = ['order_postpaid_sim'];

    /**
     * @var LeadRequestRepository
     */
    protected $leadRequestRepository;
    /**
     * @var LeadRequestRepository
     */
    private $leadCategoryRepository;
    /**
     * @var LeadRequestRepository
     */
    private $leadProductRepository;

    public function __construct(
        LeadRequestRepository $leadRequestRepository,
        LeadCategoryRepository $leadCategoryRepository,
        LeadProductRepository $leadProductRepository
    )
    {
        $this->leadRequestRepository = $leadRequestRepository;
        $this->leadCategoryRepository = $leadCategoryRepository;
        $this->leadProductRepository = $leadProductRepository;
        $this->setActionRepository($leadRequestRepository);
    }

    /**
     * @param $data
     * @return string
     */
    public function saveRequest($data)
    {
        try {
            $leadCat = $this->leadCategoryRepository->findOneByProperties(['slug' => $data['lead_category_id']], ['id']);

            if ($data['lead_category_id'] == "ecareer_programs"){
                $leadProduct = $this->leadProductRepository->findOneByProperties(['slug' => $data['lead_product_id']], ['id'])->id;
            } else {
                $leadProduct = $data['lead_product_id'];
            }

            if (!empty($data['form_data']['applicant_cv'])) {
                $data['form_data']['applicant_cv'] = $this->upload($data['form_data']['applicant_cv'], 'assetlite/ecarrer/applicant_files');
            }

            $data['lead_category_id'] = $leadCat->id;
            $data['lead_product_id'] = $leadProduct;

            $this->save($data);

//            $this->sendMail();

            return $this->sendSuccessResponse([], 'Form submitted successfully');
        } catch (\Exception $e) {
            return response()->json((['status' => 'FAIL', 'status_code' => HttpStatusCode::VALIDATION_ERROR, 'message' => $e->getMessage(), 'errors' => []]), HttpStatusCode::VALIDATION_ERROR);
        }
    }

//    public static function sendMail()
//    {
//        $data = [
//            'to' => 'jahidul@bs-23.net',
//            'subject' => "Sample Subject",
//            'message' => "One request Found"
//        ];
//        Mail::to($data['to'])->send(new LeadFoundMail($data));
////        return response('Mail send successfully');
//    }

}

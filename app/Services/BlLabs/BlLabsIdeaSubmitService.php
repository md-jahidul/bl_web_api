<?php

namespace App\Services\BlLabs;

use App\Enums\HttpStatusCode;
use App\Jobs\SendEmailJob;
use App\Models\BlLabApplication;
use App\Models\BlLabUser;
use App\Repositories\BlLabApplicationRepository;
use App\Repositories\BlLabPersonalInfoRepository;
use App\Repositories\BlLabsAuthenticationRepository;
use App\Repositories\BlLabStartUpInfoRepository;
use App\Repositories\BlLabSummaryRepository;
use App\Services\ApiBaseService;
use App\Traits\CrudTrait;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class BlLabsIdeaSubmitService extends ApiBaseService
{
    use CrudTrait;

    /**
     * @var BlLabApplicationRepository
     */
    private $blLabApplicationRepository;
    /**
     * @var BlLabStartUpInfoRepository
     */
    private $labStartUpInfoRepository;
    /**
     * @var BlLabApplicationRepository
     */
    private $labPersonalInfoRepository;
    /**
     * @var BlLabApplicationRepository
     */
    private $labSummaryRepository;

    protected const STEP_TYPES = ['summary', 'personal', 'startup'];

    /**
     * BlLabsIdeaSubmitService constructor.
     * @param BlLabApplicationRepository $labApplicationRepository
     * @param BlLabSummaryRepository $labSummaryRepository
     * @param BlLabPersonalInfoRepository $labPersonalInfoRepository
     * @param BlLabStartUpInfoRepository $labStartUpInfoRepository
     */
    public function __construct(
        BlLabApplicationRepository $labApplicationRepository,
        BlLabSummaryRepository $labSummaryRepository,
        BlLabPersonalInfoRepository $labPersonalInfoRepository,
        BlLabStartUpInfoRepository $labStartUpInfoRepository
    ) {
        $this->blLabApplicationRepository = $labApplicationRepository;
        $this->labSummaryRepository = $labSummaryRepository;
        $this->labPersonalInfoRepository = $labPersonalInfoRepository;
        $this->labStartUpInfoRepository = $labStartUpInfoRepository;
        $this->setActionRepository($labApplicationRepository);
    }

    public function storeIdea($request)
    {
        $user = Auth::user();
        $userApplication = $this->blLabApplicationRepository->findOneByProperties(['bl_lab_user_id' => $user->id, 'id_number' => $request->id_number]);

        if ($userApplication) {
            return $this->sendErrorResponse('Request Failed', 'Application not found. wrong request');
        }


        $userApplication = null;
        if ($userApplication) {
            $stepStatus = $userApplication->step_completed;
            $stepStatus = (in_array($request->step, $userApplication->step_completed)) ? $stepStatus : $userApplication->step_completed;
            $stepStatus[] = $request->step;
//            dd($stepStatus, in_array($request->step, $userApplication->step_completed));
        } else {
            $stepStatus[] = $request->step;
        }


        $applicationData = [
            'bl_lab_user_id' => $user->id,
            'application_status' => 'draft',
            'step_completed' => $stepStatus
        ];
//        dd($applicationData);
        if ($request->request_type == "create") {
            $application = $this->findAll();
            $idNumber = $application->count() + 1;
            $idNumber = str_pad($idNumber, 7, '0', STR_PAD_LEFT);
            $applicationData['id_number'] = "$idNumber";
            $userApplication = $this->save($applicationData);
        } else {
            $userApplication->update($applicationData);
        }

        dd($userApplication);


        $summary = $this->summaryData($request->all(), $user->id);
        dd($summary);
        return $this->sendSuccessResponse($response, 'Successful Attempt');
    }

    public function summaryData($data, $applicationId): array
    {
        return [
            'bl_lab_app_id' => $applicationId,
            'idea_title' => $data['idea_title'],
            'idea_details' => $data['idea_details'],
            'industry' => $data['industry'],
            'apply_for' => $data['apply_for'],
            'status' => "Complete",
        ];
    }
}

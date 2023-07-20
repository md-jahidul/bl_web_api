<?php

namespace App\Services\BlLabs;

use App\Repositories\BlLabApplicationRepository;
use App\Repositories\BlLabPersonalInfoRepository;
use App\Repositories\BlLabStartUpInfoRepository;
use App\Repositories\BlLabSummaryRepository;
use App\Services\ApiBaseService;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BlLabsIdeaSubmitService extends ApiBaseService
{
    use CrudTrait;
    use FileTrait;

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
        try {
            $user = Auth::user();
            $userApplication = $this->blLabApplicationRepository->findOneByProperties(['bl_lab_user_id' => $user->id, 'id_number' => $request->id_number]);

            if ($userApplication) {
                $stepStatus = $userApplication->step_completed;
                $stepStatus[] = $request->step;
            } else {
                $stepStatus[] = $request->step;
            }

            $applicationData = [
                'bl_lab_user_id' => $user->id,
                'application_status' => 'draft',
                'step_completed' => (isset($userApplication) && in_array($request->step, $userApplication->step_completed)) ? $userApplication->step_completed : $stepStatus
            ];

            if (!$userApplication) {
                $application = $this->findAll();
                $idNumber = $application->count() + 1;

                $idNumber = str_pad($idNumber, 7, '0', STR_PAD_LEFT);
                $applicationData['id_number'] = "$idNumber";
                $userApplication = $this->save($applicationData);
            } else {
                $userApplication->update($applicationData);
            }

            if ($request->step == "summary") {
                $this->summaryData($request->all(), $userApplication->id);
            } elseif ($request->step == "personal") {
                $this->personalData($request->all(), $userApplication->id);
            } else {
                $this->startUpData($request->all(), $userApplication->id);
            }

            return $this->sendSuccessResponse(['idea_id' => $userApplication->id_number], 'Application successfully save');
        }catch (\Exception $exception) {
            Log::channel('ideaSubmitLog')->error($exception->getMessage());
            return $this->sendErrorResponse("Failed" , $exception->getMessage());
        }
    }

    public function summaryData($data, $applicationId)
    {
        $blSummary = $this->labSummaryRepository->findOneByProperties(['bl_lab_app_id' => $applicationId]);

        $data = [
            'bl_lab_app_id' => $applicationId,
            'idea_title' => $data['idea_title'],
            'idea_details' => $data['idea_details'],
            'industry' => $data['industry'],
            'apply_for' => $data['apply_for'],
            'status' => "Complete",
        ];

        if (!$blSummary) {
            $this->labSummaryRepository->save($data);
        } else {
            $blSummary->update($data);
        }
    }

    public function personalData($data, $applicationId)
    {
        $blPersonal = $this->labPersonalInfoRepository->findOneByProperties(['bl_lab_app_id' => $applicationId]);
        if (request()->hasFile('cv')) {
            $fileName = $data['cv']->getClientOriginalName();
            $cv['file_path'] = $this->upload($data['cv'], 'lab-applicant-file');
            $cv['file_name'] = $fileName;
        }

        $memberData = [];
        if (!empty($data['team_members'])) {
            foreach ($data['team_members'] as $key => $member) {
                if (!empty($member['file'])) {
                    $fileName = $member['file']->getClientOriginalName();
                    $memberData[$key]['file_path'] = $this->upload($member['file'], 'lab-applicant-file');
                    $memberData[$key]['file_name'] = $fileName;
                }
                $memberData[$key]['name'] = $member['name'] ?? null;
                $memberData[$key]['designation'] = $member['designation'] ?? null;
                $memberData[$key]['email'] = $member['email'] ?? null;
            }
        }

        $data['bl_lab_app_id'] = $applicationId;
        $data['team_members'] = $memberData;
        $data['cv'] = $cv ?? null;
        $data['status'] = "Complete";

        if (!$blPersonal) {
            $this->labPersonalInfoRepository->save($data);
        } else {
            $blPersonal->update($data);
        }
    }

    public function startUpData($data, $applicationId)
    {
        $startUpInfo = $this->labStartUpInfoRepository->findOneByProperties(['bl_lab_app_id' => $applicationId]);

        if (request()->hasFile('business_model_file')) {
            $fileName = $data['business_model_file']->getClientOriginalName();
            $businessModelFile['file_path'] = $this->upload($data['business_model_file'], 'lab-applicant-file');
            $businessModelFile['file_name'] = $fileName;
        }

        if (request()->hasFile('gtm_plan_file')) {
            $fileName = $data['gtm_plan_file']->getClientOriginalName();
            $gtmPlanFile['file_path'] = $this->upload($data['gtm_plan_file'], 'lab-applicant-file');
            $gtmPlanFile['file_name'] = $fileName;
        }

        if (request()->hasFile('financial_metrics_file')) {
            $fileName = $data['financial_metrics_file']->getClientOriginalName();
            $financialMetricsFile['file_path'] = $this->upload($data['financial_metrics_file'], 'lab-applicant-file');
            $financialMetricsFile['file_name'] = $fileName;
        }

        $data['bl_lab_app_id'] = $applicationId;
        $data['business_model_file'] = $businessModelFile ?? null;
        $data['gtm_plan_file'] = $gtmPlanFile ?? null;
        $data['financial_metrics_file'] = $financialMetricsFile ?? null;
        $data['status'] = "Complete";

        if (!$startUpInfo) {
            $this->labStartUpInfoRepository->save($data);
        } else {
            $startUpInfo->update($data);
        }
    }

    public function ideaSubmittedData($request)
    {
        $user = Auth::user();
        $userApplication = $this->blLabApplicationRepository->findOneByProperties(['bl_lab_user_id' => $user->id, 'id_number' => $request->application_id]);


        if ($request->step == "summary") {
            $data = $this->labSummaryRepository->findOneByProperties(['bl_lab_app_id' => $userApplication->id],
                ['idea_title', 'idea_details', 'industry', 'apply_for']);
        } elseif ($request->step == "personal") {
            $data = $this->labPersonalInfoRepository->findOneByProperties(['bl_lab_app_id' => $userApplication->id],
                ['name', 'gender', 'email', 'phone_number', 'profession', 'institute_or_org', 'education', 'cv', 'team_members', 'applicant_agree']);
        } elseif ($request->step == "startup") {
            $data = $this->labStartUpInfoRepository->findOneByProperties(['bl_lab_app_id' => $userApplication->id],
                [
                    'problem_identification','big_idea','target_group','market_size','business_model','business_model_file','gtm_plan','gtm_plan_file',
                    'financial_metrics','financial_metrics_file','exist_product_service','exist_product_service_details','exist_product_service_diff','receive_fund',
                    'receive_fund_source','startup_current_stage'
                ]);
        } else {
            return $this->sendErrorResponse('Data not found', 'Request step is not found');
        }

        return $this->sendSuccessResponse($data, "Idea step information for $request->step");
    }
}

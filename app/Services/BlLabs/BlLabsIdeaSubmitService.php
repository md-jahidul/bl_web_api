<?php

namespace App\Services\BlLabs;

use App\Repositories\BlLab\BlLabApplicationRepository;
use App\Repositories\BlLab\BlLabPersonalInfoRepository;
use App\Repositories\BlLab\BlLabStartUpInfoRepository;
use App\Repositories\BlLab\BlLabSummaryRepository;
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
    protected const DRAFT = "draft";
    protected const SUBMIT = "submit";

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
                'application_status' => $request->application_status,
                'submitted_at' => ($request->application_status == "submit") ? now()->toDateString() : 'draft',
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

            return $this->sendSuccessResponse(['application_id' => $userApplication->id_number], 'Application successfully save');
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

        $teamMembers = [];
        if (isset($data['team_members_0_name']) && isset($data['team_members_count'])) {
            for ($i = 0; $i<$data['team_members_count']; $i++){
                $name = "team_members_" . $i . "_name";
                $desc = "team_members_" . $i . "_designation";
                $email = "team_members_" . $i . "_email";
                $file = "team_members_" . $i . "_file";

                if (!empty($data[$file])) {
                    $fileName = $data[$file]->getClientOriginalName();
                    $teamMembers[$i]['file_path'] = $this->upload($data[$file], 'lab-applicant-file');
                    $teamMembers[$i]['file_name'] = $fileName;
                } else {
                    $teamMembers[$i]['file_path'] = (isset($blPersonal->team_members[$i])) ? $blPersonal->team_members[$i]['file_path'] : null;
                    $teamMembers[$i]['file_name'] = (isset($blPersonal->team_members[$i])) ? $blPersonal->team_members[$i]['file_name'] : null;
                }

                $teamMembers[$i]['name'] = $data[$name];
                $teamMembers[$i]['designation'] = $data[$desc];
                $teamMembers[$i]['email'] = $data[$email];
            }
        }

        $data['bl_lab_app_id'] = $applicationId;
        $data['team_members'] = $teamMembers;
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

    public function getApplicationCurrentStage()
    {
        $user = Auth::user();
        $userApplication = $this->blLabApplicationRepository->findOneByProperties(['bl_lab_user_id' => $user->id, 'application_status' => self::DRAFT]);

        if ($userApplication) {
            $stepTypes = self::STEP_TYPES;

            $nextStage = collect($stepTypes)->filter(function ($item) use ($userApplication){
                if (!in_array($item, $userApplication->step_completed)) {
                    return $item;
                }
            })->toArray();

            $data = [
                'application_id' => $userApplication->id_number,
                'next_stage' => array_values($nextStage)[0]
            ];
            return $this->sendSuccessResponse($data, "Current applications stage");
        }

        return $this->sendErrorResponse('Application Not Found', 'The user currently has no applications running.');
    }

    public function applicationList()
    {
        $user = Auth::user();
        $applications = $this->blLabApplicationRepository->getApplications($user->id, self::SUBMIT);
        if (!empty($applications)) {
            $data = $applications->map(function ($item){
                return [
                    'application_id' => $item->id_number,
                    'idea_title' => $item->summary->idea_title,
                    'submitted_at' => $item->submitted_at
                ];
            });
            return $this->sendSuccessResponse($data, "Applications List");
        }
        return $this->sendSuccessResponse([], "You haven't submitted any ideas yet.");
    }
}

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
use App\Traits\FileTrait;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

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
            dd('ff');
        }

        return $this->sendSuccessResponse([], 'Application successful store');
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
            $cv = [];
            foreach ($data['cv'] as $key => $file) {
                $fileName = $file->getClientOriginalName();
                $cv[$key]['file_path'] = $this->upload($file, 'lab-applicant-file');
                $cv[$key]['file_name'] = $fileName;
            }
        }

        $data = [
            'bl_lab_app_id' => $applicationId,
            'name' => $data['name'],
            'gender' => $data['gender'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'institute_or_org' => $data['institute_or_org'],
            'education' => $data['education'],
            'team_members' => $data['team_members'],
            'applicant_agree' => $data['applicant_agree'],
            'cv' => $cv,
            'status' => "Complete",
        ];

        if (!$blPersonal) {
            $this->labPersonalInfoRepository->save($data);
        } else {
            $blPersonal->update($data);
        }
    }
}

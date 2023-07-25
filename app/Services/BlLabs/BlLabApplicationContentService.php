<?php

namespace App\Services\BlLabs;

use App\Repositories\BlLab\BlLabApplicationRepository;
use App\Repositories\BlLab\BlLabEducationRepository;
use App\Repositories\BlLab\BlLabIndustryRepository;
use App\Repositories\BlLab\BlLabInstituteOrgRepository;
use App\Repositories\BlLab\BlLabPersonalInfoRepository;
use App\Repositories\BlLab\BlLabProfessionRepository;
use App\Repositories\BlLab\BlLabProgramRepository;
use App\Repositories\BlLab\BlLabStartUpInfoRepository;
use App\Repositories\BlLab\BlLabSummaryRepository;
use App\Services\ApiBaseService;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BlLabApplicationContentService extends ApiBaseService
{
    /**
     * @var BlLabIndustryRepository
     */
    private $blLabIndustryRepository;
    /**
     * @var BlLabProgramRepository
     */
    private $blLabProgramRepository;
    /**
     * @var BlLabProfessionRepository
     */
    private $blLabProfessionRepository;
    /**
     * @var BlLabInstituteOrgRepository
     */
    private $blLabInstituteOrgRepository;
    /**
     * @var BlLabEducationRepository
     */
    private $blLabEducationRepository;

    /**
     * BlLabApplicationContentService constructor.
     * @param BlLabIndustryRepository $blLabIndustryRepository
     */
    public function __construct(
        BlLabIndustryRepository $blLabIndustryRepository,
        BlLabProgramRepository $blLabProgramRepository,
        BlLabProfessionRepository $blLabProfessionRepository,
        BlLabInstituteOrgRepository $blLabInstituteOrgRepository,
        BlLabEducationRepository $blLabEducationRepository
    ) {
        $this->blLabIndustryRepository = $blLabIndustryRepository;
        $this->blLabProgramRepository = $blLabProgramRepository;
        $this->blLabProfessionRepository = $blLabProfessionRepository;
        $this->blLabInstituteOrgRepository = $blLabInstituteOrgRepository;
        $this->blLabEducationRepository = $blLabEducationRepository;
//        $this->setActionRepository($labApplicationRepository);
    }

    public function industry()
    {
        $data = $this->blLabIndustryRepository->findByProperties(['status' => 1], ['name_en']);
        return $this->sendSuccessResponse($data, 'Industry List');
    }

    public function program()
    {
        $data = $this->blLabProgramRepository->findByProperties(['status' => 1], ['name_en', 'icon', 'is_clickable']);
        return $this->sendSuccessResponse($data, 'Program List');
    }

    public function profession()
    {
        $data = $this->blLabProfessionRepository->findByProperties(['status' => 1], ['name_en']);
        return $this->sendSuccessResponse($data, 'Profession List');
    }

    public function instituteOrOrg()
    {
        $data = $this->blLabInstituteOrgRepository->findByProperties(['status' => 1], ['name_en']);
        return $this->sendSuccessResponse($data, 'Institute/Organization List');
    }

    public function education()
    {
        $data = $this->blLabEducationRepository->findByProperties(['status' => 1], ['name_en']);
        return $this->sendSuccessResponse($data, 'Education List');
    }
}

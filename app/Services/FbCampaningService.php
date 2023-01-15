<?php

namespace App\Services;
use Illuminate\Http\Response;
use App\Repositories\FbCampaningRepository;
use App\Repositories\FaqRepository;

class FbCampaningService extends ApiBaseService
{
    /**
     * @var FbCampaningRepository
     */
    private $fbCampaningRepository;

    /**
     * AboutPageService constructor.
     * @param FbCampaningRepository $fbCampaningRepository
     */
    public function __construct(FbCampaningRepository $fbCampaningRepository)
    {
        $this->fbCampaningRepository = $fbCampaningRepository;
    }

    public function storeData($data)
    {
        $this->fbCampaningRepository->save($data);
        return new Response("FB Campaning data has been successfully created");
    }
}

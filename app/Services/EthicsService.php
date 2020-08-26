<?php

namespace App\Services;

use App\Repositories\EthicsRepository;
use Exception;

/**
 * Class EthicsService
 * @package App\Services
 */
class EthicsService extends ApiBaseService
{

    /**
     * @var EthicsRepository
     */
    protected $ethicsRepository;


    /**
     * EthicsService constructor.
     * @param EthicsRepository $ethicsRepository
     */
    public function __construct(EthicsRepository $ethicsRepository)
    {
        $this->ethicsRepository = $ethicsRepository;
    }

    /**
     * Request for ethics data
     *
     * @return mixed|string
     */
    public function getData()
    {
        try {
            $data = $this->ethicsRepository->getPageInfo();
            $data['files'] = $this->ethicsRepository->getFiles();
            return $this->sendSuccessResponse($data, 'Ethics data');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }
}

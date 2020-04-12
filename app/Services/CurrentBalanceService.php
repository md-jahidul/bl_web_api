<?php

namespace App\Services;

use App\Repositories\CurrentBalanceRepository;
use Exception;

/**
 * Class CurrentBalanceService
 * @package App\Services
 */
class CurrentBalanceService extends ApiBaseService
{

    /**
     * @var CurrentBalanceRepository
     */
    protected $currentBalanceRepository;


    /**
     * CurrentBalanceService constructor.
     * @param CurrentBalanceRepository $currentBalanceRepository
     */
    public function __construct(CurrentBalanceRepository $currentBalanceRepository)
    {
        $this->currentBalanceRepository = $currentBalanceRepository;
    }


    public function getCurrentBalance()
    {
        try {
            $data = $this->currentBalanceRepository->getCurrentBalance();
            return $this->sendSuccessResponse($data, 'Current Balance Info');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }
}

<?php
/**
 * Dev: Bulbul Mahmud Nito
 * Date: 24/06/2020
 */

namespace App\Http\Controllers\API\V1;


use App\Http\Controllers\Controller;
use App\Services\EthicsService;

class EthicsController extends Controller
{
    /**
     * @var EthicsService
     */
    protected $ethicsService;

    /**
     * EthicsController constructor.
     * @param EthicsService $ethicsService
     */
    public function __construct(EthicsService $ethicsService)
    {
        $this->ethicsService = $ethicsService;
    }
    
    public function index(){
        return $this->ethicsService->getData();
    }
    
   
    



}

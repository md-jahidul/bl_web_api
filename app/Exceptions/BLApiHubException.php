<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 12/10/19
 * Time: 4:46 PM
 */

namespace App\Exceptions;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BLApiHubException extends \Exception
{
    /**
     * Render an exception into an HTTP response.
     *
     * @return JsonResponse
     */
    public function render()
    {
        Log::error('BL API HUB Error: code - '.$this->getCode(). 'Error message: ' . $this->getMessage());
        $response = [
            'status' => 'FAIL',
            'status_code' => 500,
            'message' => $this->message,
        ];
        return response()->json($response, 500);
    }
}

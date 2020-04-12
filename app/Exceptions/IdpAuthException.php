<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 12/10/19
 * Time: 4:51 PM
 */

namespace App\Exceptions;


use Illuminate\Support\Facades\Log;

class IdpAuthException extends \Exception
{
    /**
     * Render an exception into an HTTP response.
     *
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        Log::error('IDP Error: code - '.$this->getCode(). 'Error message: ' . $this->getMessage());

        $response = [
            'status' => 'FAIL',
            'status_code' => 401,
            'message' => $this->message,
        ];
        return response()->json($response, 401);
    }
}

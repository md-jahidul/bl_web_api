<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 12/10/19
 * Time: 4:46 PM
 */

namespace App\Exceptions;


class BLApiHubException extends \Exception
{
    /**
     * Render an exception into an HTTP response.
     *
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        $response = [
            'status' => 'FAIL',
            'status_code' => 500,
            'message' => $this->message,
        ];
        return response()->json($response, 500);
    }
}

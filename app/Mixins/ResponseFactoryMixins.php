<?php

namespace App\Mixins;

class ResponseFactoryMixins 
{
    public function success(){
        return function ($result, $message = 'Items retrieved successfully') {
            $response = [
                'success' => true,
                'status' => 200,
                'message' => $message,
                'data'    => $result,
            ];
    
            return response()->json($response, 200);
        };
    }

    public function error(){
        return function ($error = 'Validation Error', $errorMessages = [], $code = 404) {
            $response = [
                'success' => false,
                'status' => 404,
                'message' => $error,
                'data' => []
            ];    
    
            if(!empty($errorMessages)){
                $response['error'] = $errorMessages;
            }    
    
            return response()->json($response, $code);
        };
    }
}

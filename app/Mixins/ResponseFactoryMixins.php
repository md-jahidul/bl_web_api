<?php

namespace App\Mixins;

class ResponseFactoryMixins 
{
    public function success(){
        return function ($result, $message = 'Items retrieved successfully') {
            $response = [
                'success' => true,
                'data'    => $result,
                'message' => $message,
            ];    
    
            return response()->json($response, 200);
        };
    }

    public function error(){
        return function ($error = 'Validation Error', $errorMessages = [], $code = 404) {
            $response = [
                'success' => false,
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

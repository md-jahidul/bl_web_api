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
                'status' => $code,
                'message' => $error,
            ];    
    
            if(!empty($errorMessages)){
                $response['error'] = explode('|', $errorMessages->getMessage());
            }    
    
            return response()->json($response, $code);
        };
    }
}

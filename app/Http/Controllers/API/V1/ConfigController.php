<?php

namespace App\Http\Controllers\API\V1;


use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Config;


class ConfigController extends Controller
{
    

    /**
     * Get image upload size form config table for frontend customer
     * @return [number] [Image size in KB]
     */
    public static function customerImageUploadSize(){

        $config_key = Config::where('key', '=', 'image_upload_size')->first();

        if( !empty($config_key) ){
            $file_size = ((int)$config_key->value * 1024);
            return $file_size;
        }
        else{
            return (1 * 1024);
        }

    }

    /**
     * [Image upload type for frontend customer]
     * @return [mixed] [description]
     */
    public static function customerImageUploadType($type_array = false){

        $config_key = Config::where('key', '=', 'image_upload_type')->first();

        if( !empty($config_key) ){

            if( $type_array ){
                return explode(',', $config_key->value);
            }
            else{
                return $config_key->value;
            }
        }
        else{
            return 'jpeg,png';
        }

    }

}

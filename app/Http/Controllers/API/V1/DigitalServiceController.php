<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DigitalServiceController extends Controller
{
    public function getDigitalService()
    {

        try {
            $digitalService = [
                [
                    "id" => 1,
                    "title" => "Robiul Islam",
                    "description" => "Banglalink provide the fastest internet throughout the country, I never get the best experience except using Banlalink. It’s awesome service ever, I’ll always use Banglalink.",
                    "short_note" => "Studiomaqs",
                    "image_url" => "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                    "alt_text" => "Banglalink clients",
                    "ratings" => 4.5
                ],
                [
                    "id" => 2,
                    "title" => "Shahriar Ahmed",
                    "description" => "Banglalink provide the fastest internet throughout the country, I never get the best experience except using Banlalink. It’s awesome service ever, I’ll always use Banglalink.",
                    "short_note" => "Studiomaqs",
                    "image_url" => "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                    "alt_text" => "Banglalink clients",
                    "ratings" => 4.5
                ],
                [
                    "id" => 3,
                    "title" => "Shahriar Ahmed",
                    "description" => "Banglalink provide the fastest internet throughout the country, I never get the best experience except using Banlalink. It’s awesome service ever, I’ll always use Banglalink.",
                    "short_note" => "Studiomaqs",
                    "image_url" => "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                    "alt_text" => "Banglalink clients",
                    "ratings" => 4.5
                ],
                [
                    "id" => 4,
                    "title" => "Shahriar Ahmed",
                    "description" => "Banglalink provide the fastest internet throughout the country, I never get the best experience except using Banlalink. It’s awesome service ever, I’ll always use Banglalink.",
                    "short_note" => "Studiomaqs",
                    "image_url" => "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                    "alt_text" => "Banglalink clients",
                    "ratings" => 4.5
                ]
            ];
            if (isset($digitalService)) {
                return response()->json(
                    [
                        'status' => 200,
                        'success' => true,
                        'message' => 'Data Found!',
                        'data' => $digitalService
                    ]
                );
            }
            return response()->json(
                [
                    'status' => 400,
                    'success' => false,
                    'message' => 'Data Not Found!'
                ]
            );
        } catch (QueryException $e) {
            return response()->json(
                [
                    'status' => 403,
                    'success' => false,
                    'message' => explode('|', $e->getMessage())[0],
                ]
            );
        }


    }
}

<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 11/21/19
 * Time: 2:12 PM
 */

namespace App\Http\Controllers\API\V1;


use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
    protected $userService;

    /**
     * UserProfileController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    public function view(Request $request)
    {
        $userDetails = $this->userService->viewProfile($request);
        return $userDetails;
    }

    public function update(Request $request)
    {
        return $this->userService->updateProfile($request);
    }

    public function updateProfileImage(Request $request)
    {
        if ($request->hasFile('profile_photo')) {

            // TODO: Done:check file size validation
            $validator = Validator::make($request->all(), [
                'profile_photo' => 'required|mimes:jpeg,png|max:2000' // 2M
            ]);
            if ($validator->fails()) {
                return response()->json($validator->messages(), HttpStatusCode::VALIDATION_ERROR);
            }
            

            return $this->userService->uploadProfileImage($request);
        } else {
            return response()->json(['profile_photo' => 'Profile photo is required'], HttpStatusCode::VALIDATION_ERROR);
        }
    }

    public function removeProfileImage(Request $request)
    {
        return $this->userService->removeProfileImage($request);
    }
}

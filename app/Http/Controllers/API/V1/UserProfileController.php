<?php
namespace App\Http\Controllers\API\V1;


use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\V1\ConfigController;

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
        return $this->userService->viewProfile($request);
    }

    public function update(Request $request)
    {
        # Image validation check
        $image_upload_size = ConfigController::customerImageUploadSize();
        $image_upload_type = ConfigController::customerImageUploadType();

        $validator = Validator::make($request->all(), [
            'profile_photo' => 'nullable|mimes:'.$image_upload_type.'|max:'.$image_upload_size // 2M
        ]);
        if ($validator->fails()) {
            // return response()->json($validator->messages()->first(), HttpStatusCode::VALIDATION_ERROR);
            return response()->json((['status' => 'FAIL', 'status_code' => HttpStatusCode::VALIDATION_ERROR, 'message' =>  $validator->messages()->first(), 'errors' => [] ]), HttpStatusCode::VALIDATION_ERROR);
        }

        return $this->userService->updateProfile($request);
    }

    public function updateProfileImage(Request $request)
    {

        if ($request->hasFile('profile_photo')) {

            // TODO: Done:check file size validation
            $image_upload_size = ConfigController::customerImageUploadSize();
            $image_upload_type = ConfigController::customerImageUploadType();

            $validator = Validator::make($request->all(), [
                'profile_photo' => 'required|mimes:'.$image_upload_type.'|max:'.$image_upload_size // 2M
            ]);
            if ($validator->fails()) {
                // return response()->json($validator->messages()->first(), HttpStatusCode::VALIDATION_ERROR);
                return response()->json((['status' => 'FAIL', 'status_code' => HttpStatusCode::VALIDATION_ERROR, 'message' =>  $validator->messages()->first(), 'errors' => [] ]), HttpStatusCode::VALIDATION_ERROR);
            }


            return $this->userService->uploadProfileImage($request);
        } else {
            // return response()->json(['profile_photo' => 'Profile photo is required'], HttpStatusCode::VALIDATION_ERROR);
            return response()->json((['status' => 'FAIL', 'status_code' => HttpStatusCode::VALIDATION_ERROR, 'message' =>  'Profile photo is required', 'errors' => [] ]), HttpStatusCode::VALIDATION_ERROR);
        }
    }

    public function removeProfileImage(Request $request)
    {
        return $this->userService->removeProfileImage($request);
    }
}

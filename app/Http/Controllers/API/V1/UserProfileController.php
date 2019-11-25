<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 11/21/19
 * Time: 2:12 PM
 */

namespace App\Http\Controllers\API\V1;


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
        $mobile = $request['mobile'];
        $userDetails = $this->userService->viewProfile($mobile);
        return $userDetails;
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), ['mobile' => 'required']);
        if ($validator->fails()) {
            return response()->json($validator->messages(), HttpStatusCode::VALIDATION_ERROR);
        }

        return $this->userService->updateProfile($request);
    }
}

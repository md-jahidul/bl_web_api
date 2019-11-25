<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Repositories\CustomerRepository;
use Exception;
use Illuminate\Http\Request;
use App\Repositories\ShortcutRepository;
use App\Http\Resources\ShortcutResource;

/**
 * Class ShortcutService
 * @package App\Services
 */
class ShortcutService extends ApiBaseService
{

    /**
     * @var $shortcutRepository
     */
    protected $shortcutRepository;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * ShortcutService constructor.
     * @param ShortcutRepository $shortcutRepository
     * @param CustomerRepository $customerRepository
     */
    public function __construct(ShortcutRepository $shortcutRepository, CustomerRepository $customerRepository)
    {
        $this->shortcutRepository = $shortcutRepository;
        $this->customerRepository = $customerRepository;
    }


    /**
     * Get shortcut list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllShortcut()
    {
        try {
            $shortcuts = $this->shortcutRepository->getAllShortcut();
            $formatted_data = ShortcutResource::collection($shortcuts);
            return $this->sendSuccessResponse(
                $formatted_data,
                'All Available Shortcuts',
                [],
                HttpStatusCode::SUCCESS
            );
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }

    /**
     * Retrieve Shortcut list
     *
     * @param $request
     * @return mixed|string
     */
    public function getShortcutWithUser($request)
    {
        $formatted_data = [];
        $limit = $this->shortcutRepository->getShortcutLimit();

        $bearerToken = ['token' => $request->header('authorization')];


        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $data = json_decode($response, true);


        if ($data['token_status'] != 'Valid') {
            return $this->sendErrorResponse("Token is Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        $user = $this->customerRepository->getCustomerInfoByPhone($data['user']['mobile']);

        if (!$user) {
            return $this->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        try {
            $shortcuts = $this->shortcutRepository->getShortcutWithUser($user->id);

            if (!empty($shortcuts)) {
                $formatted_data = ShortcutResource::collection($shortcuts);
            }

            $data = ["limit" => $limit, "shortcuts" => $formatted_data];

            return $this->sendSuccessResponse($data, 'Shortcut list', [], HttpStatusCode::SUCCESS);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }


    /**
     * Check if shortcut limit reached
     *
     * @param Request $request
     * @return mixed
     */
    public function checkIfShortcutLimitReached($request)
    {
        return $this->shortcutRepository->checkShortcutLimit($request);
    }


    /**
     * Count shortcut
     *
     * @param $user_id
     * @return mixed
     */
    public function getCurrentShortcutCount($user_id)
    {
        return $this->shortcutRepository->getCurrentShortcutCount($user_id);
    }

    /**
     * Check If Already Added
     *
     * @param $user_id
     * @param $shortcut_id
     * @return mixed
     */
    public function checkIfAlreadyAdded($user_id, $shortcut_id)
    {
        return $this->shortcutRepository->checkExistOrNot($user_id, $shortcut_id);
    }

    /**
     * Add shortcut to user profile
     *
     * @param $request
     * @return string
     */

    public function addShortcutToUserProfile(Request $request)
    {
        $formatted_data = [];

        $bearerToken = ['token' => $request->header('authorization')];

        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $data = json_decode($response, true);

        if ($data['token_status'] != 'Valid') {
            return $this->sendErrorResponse("Token Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        $user = $this->customerRepository->getCustomerInfoByPhone($data['user']['mobile']);

        if (!$user) {
            return $this->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        try {
            $message = 'Can not add any more shortcut. You have reached to your limit';
            if ($this->checkIfShortcutLimitReached($request)) {
                return $this->sendErrorResponse($message, [], HttpStatusCode::BAD_REQUEST);
            }


            /*
            $current_total_shortcut    = $this->getCurrentShortcutCount($request->user_id);
            $remaining_total_shortcut  = env('SHORTCUT_LIMIT') - $current_total_shortcut;
            $new_total_shortcut        = $current_total_shortcut + count($request->shortcut_id);

            if($new_total_shortcut > env('SHORTCUT_LIMIT')){
                return $this->sendErrorResponse(
                    'Sorry. You can add only ' .$remaining_total_shortcut .' more shortcut(s)',
                    [], HttpStatusCode::BAD_REQUEST);
            }

            $shortcuts = $request->shortcut_id;

            foreach ($shortcuts as $shortcut_id) {
                if ($this->checkIfAlreadyAdded($request->user_id, $shortcut_id)) {
                    return $this->sendErrorResponse('One of these shortcut is already added',
                        [], HttpStatusCode::BAD_REQUEST);
                }
            }*/


            $shortcuts =  $request->input('shortcut_id');

            $this->shortcutRepository->addMultipleShortcutToUserProfile($shortcuts, $user->id);

           /* $shortcuts = $this->shortcutRepository->getShortcutWithUser($user->id);


            if (!empty($shortcuts)) {

                $formatted_data = ShortcutResource::collection($shortcuts);
            }*/

            $message = 'Shortcut Successfully added';
            return $this->sendSuccessResponse([], $message, [], HttpStatusCode::SUCCESS);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }

    /**
     * Remove shortcut from user profile
     *
     * @param $request
     * @return string
     */
    public function removeShortcutFromUserProfile($request)
    {
        $formatted_data = [];
        $shortcuts = $request->shortcut_id;

        $bearerToken = ['token' => $request->header('authorization')];

        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $data = json_decode($response, true);

        if ($data['token_status'] != 'Valid') {
            return $this->sendErrorResponse("Token is Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        $user = $this->customerRepository->getCustomerInfoByPhone($data['user']['mobile']);

        if (!$user) {
            return $this->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }


        try {
            foreach ($shortcuts as $shortcut_id) {
                if ($this->shortcutRepository->checkDefaultShortcut($shortcut_id)) {
                    return $this->sendErrorResponse(
                        'This is default.You can not remove this',
                        [],
                        HttpStatusCode::BAD_REQUEST
                    );
                }

                if (!$this->shortcutRepository->checkExistOrNot($user->id, $shortcut_id)) {
                    return $this->sendErrorResponse(
                        'Cannot remove.This Shortcut is not added to this user',
                        [],
                        HttpStatusCode::BAD_REQUEST
                    );
                }
            }


            $this->shortcutRepository->removeMultipleShortcutFromUserProfile($user->id, $shortcuts);

           /* $shortcuts = $this->shortcutRepository->getShortcutWithUser($user->id);


            if (!empty($shortcuts)) {

                $formatted_data = ShortcutResource::collection($shortcuts);
            }*/

            $message = 'Successfully removed shortcut';
            return $this->sendSuccessResponse($formatted_data, $message, [], HttpStatusCode::SUCCESS);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }

    /**
     * Arrange short cut
     *
     * @param $request
     * @return string
     */
    public function arrangeShortcut($request)
    {
        $shortcuts = $request->shortcut_id;

        $bearerToken = ['token' => $request->header('authorization')];

        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $data = json_decode($response, true);

        if ($data['token_status'] != 'Valid') {
            return $this->sendErrorResponse("Token Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        $user = $this->customerRepository->getCustomerInfoByPhone($data['user']['mobile']);

        try {
            /*foreach ($shortcuts as $shortcut_id) {
                if (!$this->shortcutRepository->checkExistOrNot($request->user_id, $shortcut_id)) {
                    return $this->sendErrorResponse(
                        'Cannot arrange.This Shortcut is not added to this user',
                        [],
                        HttpStatusCode::BAD_REQUEST
                    );
                }
            }*/

            $response_add = $this->shortcutRepository->addMultipleShortcutToUserProfile($shortcuts, $user->id);

            if ($response_add) {
                $this->shortcutRepository->arrangeShortcut($user->id, $shortcuts);

                $message = 'Successfully Arrange shortcut';
                return $this->sendSuccessResponse([], $message, [], HttpStatusCode::SUCCESS);
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }
}

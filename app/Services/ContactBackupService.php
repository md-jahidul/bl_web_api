<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Resources\ContactBackupCollection;
use App\Http\Resources\ContactBackupResource;
use App\Http\Resources\UssdCodeResource;
use App\Models\CustomerContactBackup;
use App\Repositories\CustomerRepository;
use App\Repositories\UssdCodeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactBackupService extends ApiBaseService
{

    /**
     * @var CustomerRepository
     */
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function storeContactBackup(Request $request)
    {
        $user = $this->customerService->getAuthenticateCustomer($request);

        if (!$user) {
            return $this->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        CustomerContactBackup::create([
            'customer_id' => $user->id,
            'contact_backup' => json_encode($request->data),
            'total_contact' => count($request->data)
        ]);

        return $this->sendSuccessResponse(
            [],
            'Successfully Backup'
        );
    }

    public function getBackupListByCustomer(Request $request)
    {
        $builder = new CustomerContactBackup();
        $page_no = 1;
        $item_per_page = 10;

        $user = $this->customerService->getAuthenticateCustomer($request);

        if (!$user) {
            return $this->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        if ($request->has('page_no')) {
            $page_no = $request->page_no;
        }

        if ($request->has('item_per_page')) {
            $item_per_page = $request->item_per_page;
        }

        $builder = $builder->select('id', 'total_contact', 'created_at')->where('customer_id', $user->id);

        $data = $builder->latest()->paginate($item_per_page, ['*'], null, $page_no);


        $formatted_data = new ContactBackupCollection($data);

        return $this->sendSuccessResponse(
            $formatted_data,
            'contact backup list'
        );
    }

    public function getBackupDetails(Request $request)
    {
        $user = $this->customerService->getAuthenticateCustomer($request);

        if (!$user) {
            return $this->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        $contact = CustomerContactBackup::where([
            ['customer_id', '=', $user->id],
            ['id', '=', $request->contact_backup_id]
        ])->first();

        if (!$contact) {
            return $this->sendErrorResponse(
                "This backup not belongs to this user",
                [],
                HttpStatusCode::BAD_REQUEST
            );
        }
        $formatted_data = [
            'total_contact' => $contact->total_contact,
            'contacts' => json_decode($contact->contact_backup, true)
        ];

        return $this->sendSuccessResponse(
            $formatted_data,
            'contact backup details'
        );
    }
}

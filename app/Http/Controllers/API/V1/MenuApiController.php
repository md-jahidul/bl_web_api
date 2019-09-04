<?php


namespace App\Http\Controllers\API\V1;


use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Database\QueryException;

class MenuApiController extends Controller
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMenu()
    {
        try{
            $menu = Menu::with('children')
                        ->where('parent_id', 0)
                        ->where('status', 1)
                        ->get();
            if (isset($menu)) {
                return response()->json(
                    [
                        'status' => 200,
                        'success' => true,
                        'message' => 'Data Found!',
                        'data' => $menu
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
        }catch (QueryException $e) {
            return response()->json(
                [
                    'status' => 403,
                    'success' => false,
                    'message' => explode('|', $e->getMessage())[0],
                    'description' => explode('|', $e->getMessage())[1]
                ]
            );
        }
    }
}

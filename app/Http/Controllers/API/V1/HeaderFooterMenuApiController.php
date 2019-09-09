<?php

namespace App\Http\Controllers\API\V1;


use App\Http\Controllers\Controller;
use App\Models\FooterMenu;
use App\Models\Menu;
use Illuminate\Database\QueryException;

class HeaderFooterMenuApiController extends Controller
{
    public function getFooterMenu()
    {
        try{
            $headerMenu = Menu::with('children')
                ->where('parent_id', 0)
                ->where('status', 1)
                ->get();
            $footerMenu = FooterMenu::with('children')
                                    ->where('parent_id', 0)
                                    ->where('status', 1)
                                    ->get();
            if (isset($footerMenu)) {
                return response()->json(
                    [
                        'status' => 200,
                        'success' => true,
                        'message' => 'Data Found!',
                        'data' => [
                            'header' => [
                                'menu' => $headerMenu
                            ],
                            'footer' => [
                               'menu' => $footerMenu
                            ]
                        ]
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
                    'error-message' => explode('|', $e->getMessage())[0],
                ]
            );
        }
    }
}

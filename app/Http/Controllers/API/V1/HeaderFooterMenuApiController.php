<?php

namespace App\Http\Controllers\API\V1;


use App\Http\Controllers\Controller;
use App\Models\FooterMenu;
use App\Models\Menu;
use App\Models\Config;
use Illuminate\Database\QueryException;

class HeaderFooterMenuApiController extends Controller
{
    public function getFooterMenu()
    {
        try{
            $headerMenu = Menu::with('children')
                            ->where('parent_id', 0)
                            ->where('status', 1)
                            ->orderBy('display_order')
                            ->get();

            $footerMenu = FooterMenu::with('children')
                                    ->where('parent_id', 0)
                                    ->where('status', 1)
                                    ->orderBy('display_order')
                                    ->get();


            $h_settings = Config::where('key','site_logo')
                        ->orWhere('key','logo_alt_text')
                        ->get();

            $header_settings = [];                        
            foreach ($h_settings as $settings) {
                $header_settings[ $settings->key ] =  $settings->value;
            }

           

            $f_settings = Config::whereNotIn('key',['site_logo','logo_alt_text'])
                                      ->get();

            $footer_settings = [];                        
            foreach ($f_settings as $settings) {
                $footer_settings[$settings->key] = $settings->value;
            }

            

            if (isset($footerMenu)) {
                return response()->json(
                    [
                        'status' => 200,
                        'success' => true,
                        'message' => 'Data Found!',
                        'data' => [
                            'header' => [
                                'menu' => $headerMenu,
                                'settings' => $header_settings
                            ],
                            'footer' => [
                               'menu' => $footerMenu,
                               'settings' => $footer_settings
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

    public function getConfig()
    {
        try{
            $headerMenu = Menu::with('children')
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

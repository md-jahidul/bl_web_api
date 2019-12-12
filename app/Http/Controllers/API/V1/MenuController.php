<?php

namespace App\Http\Controllers\API\V1;


use App\Http\Controllers\Controller;
use App\Models\FooterMenu;
use App\Models\Menu;
use App\Models\Config;
use Illuminate\Database\QueryException;

class MenuController extends Controller
{
    public function getHeaderFooterMenus()
    {
        try {
            $headerMenus = Menu::where('parent_id', 0)
                ->where('status', 1)
                ->with(['children' => function($query){
                    $query->where('status', 1);
                }])
                ->orderBy('display_order')
                ->get();

            $footerMenu = FooterMenu::where('parent_id', 0)
                ->where('status', 1)
                ->with(['children' => function($query){
                    $query->where('status', 1);
                }])
                ->orderBy('display_order')
                ->get();

            $h_settings = Config::where('key', 'site_logo')
                ->orWhere('key', 'logo_alt_text')
                ->get();

            $header_settings = [];
            foreach ($h_settings as $settings) {
                $header_settings[$settings->key] = $settings->value;
            }

            $f_settings = Config::whereNotIn('key', ['site_logo', 'logo_alt_text'])->get();
            $footer_settings = [];
            foreach ($f_settings as $settings) {
                $footer_settings[$settings->key] = $settings->value;
            }

            if (isset($footerMenu) && isset($headerMenus)) {
                $result = [
                    'header' => [
                        'menu' => $headerMenus,
                        'settings' => $header_settings
                    ],
                    'footer' => [
                        'menu' => $footerMenu,
                        'settings' => $footer_settings
                    ]
                ];

                return response()->success($result, 'Data Found!');
            }

            return response()->error('Data Not Found!');
        } catch (QueryException $e) {
            return response()->error('Data Not Found!', $e->getMessage());
        }
    }
}

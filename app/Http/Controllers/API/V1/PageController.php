<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Page\NewPage as Page;
use App\Services\Page\PageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * @var PageService
     */
    private $pageService;

    /**
     * PageService constructor.
     * @param PageService $pageService
     */
    public function __construct(
        PageService $pageService
    ) {
        $this->pageService = $pageService;
    }

    /**
     * Fetches the page data with the specified page slug
     *
     * @param Request $request
     * @return array
     */
    public function view(Request $request) {
        $page_slug = $request->route('slug', null);
        $query = null;
        $message = "Page has been fetched successfully";
        $result = array(
            'status' => 'SUCCESS',
            'status_code' => 200,
            'message' => $message,
            'data' => ['page' => null]
        );

        if($page_slug && $page_slug !== ""){
            $query = Page::select("id", "name", "url_slug", "page_header_en", "page_header_bn", "schema_markup")->where('url_slug', $page_slug)->where('status', 1)->first();
        }

        if ( ! $query ) {
            $result['message'] = 'Page not found';
            return response()->json($result, 200);
        }

        $tab_component_types = [
            'tab-component',
            'tab_component_with_image_card_one',
            'tab_component_with_image_card_two',
            'tab_component_with_image_card_three',
            'tab_component_with_image_card_four'
        ];
        if($query && isset($query->pageComponents)){
            $query->page_components = $query->pageComponents->each(function ($component) use($tab_component_types) {
                if(in_array($component->type, $tab_component_types)){
                    $component_child_data = $component->componentData->map(function ($group) use($component) {
                        $items = $group->menuTreeWithHierarchy($component->id)->toArray();
                        return ($items);
                    })->values()->all();
//                    dd($component_child_data);
                    $component->data = count($component_child_data) ? $this->tabDataItemFormatted($component_child_data[0]) : [];
                }else{
                    //->select("id","page_id","name","type","order", "status")
                    $comData = $this->componentDataItemFormatted($component->componentData);
                    $component->data = array_values($comData);
//                    return array_values($comData);
//                    $component->data = $component->componentData->groupBy('order')->map(function ($group) {
//                        $items = $group->toArray();
//                        return isset($items) ? $this->componentDataItemFormatted($items): null;
//                    })->values()->all();
                }
            })->map(function ($data){
                $row = $data->toArray();
                if(isset($row['component_data'])) unset($row['component_data']);
                return $row;
            });
        }

        if ( ! $query ) {
            $result['message'] = 'Page not found';
            // return ApiResponse::success(['page' => null], 'Page not found', 200, 403);
            return response()->json($result, 200);
        }
        $result['data']['page'] = $this->componentDataFormatted($query);
        return response()->json($result, 200);
    }

    /**
     * @param $slug
     * @return JsonResponse|mixed
     */
    public function getPageComponents($slug)
    {
        return $this->pageService->pageComponents($slug);
    }

    protected function componentDataFormatted($page){
        $page = $page->toArray();
        if(isset($page['page_components_query'])) unset($page['page_components_query']);
        return $page;
    }

    protected function componentDataItemFormatted($items){
        $data = array();
        if(count($items)){
            foreach($items as $item){
                $data[$item['group']][$item['key']] = array(
                    'en'=> $item['value_en'],
                    'bn'=> $item['value_bn']
                );
                $children = isset($item['items']) ? $item['items'] : array();
                if(count($children)){
                    $data[$item['key']]['items'] = $this->componentDataItemFormatted($children);
                }
            }
        }
        return $data;
    }

    protected function tabDataItemFormatted($tabs){
        // return $tabs;
        $data = array();
        if(count($tabs)){
            foreach($tabs as $key => $tab){
                $_tab = $tab;
                $arr = [];
                $items = isset($tab['items']) ? $tab['items'] : array();
                if(count($items)){
                    unset($_tab['items']);
                    $arr2 = [];
                    foreach($items as $item){
                        if ($item['key'] == "content_type" || $item['key'] == "static_component") {
                            $arr[$item['key']] = array(
                                'en'=> $item['value_en'],
                                'bn'=> $item['value_bn']
                            );
                            continue;
                        }
                        $integerNumber = $item['group'] * 10;
                        $group = (string )$integerNumber;

                        $arr2['items'][$group][$item['key']] = array(
                            'en'=> $item['value_en'],
                            'bn'=> $item['value_bn']
                        );
                    }
                    // $arr = $_tab;
                    $arr[$_tab['key']] = array(
                        'en'=> $_tab['value_en'],
                        'bn'=> $_tab['value_bn']
                    );
                    $arr['items'] = isset($arr2['items']) ? array_values($arr2['items']) : [];
                }else{
                    $arr = $tab;
                    $arr['items'] = array();
                }
                $data[$key] = $arr;
            }
        }
        return $data;
    }
}

<?php

namespace App\Services\Page;

use App\Repositories\Page\PageRepository;
use App\Services\ApiBaseService;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Support\Facades\Redis;

class PageService extends ApiBaseService
{
    use CrudTrait;
    use FileTrait;
    private $pageRepository;

    protected const REDIS_PAGE_KEY = "new_page_components";

    /**
     * PageService constructor.
     * @param PageRepository $pageRepository
     */
    public function __construct(
        PageRepository $pageRepository
    ) {
        $this->pageRepository = $pageRepository;
        $this->setActionRepository($pageRepository);
    }

    public function pageComponents($slug)
    {
        try {
            $redisKey = self::REDIS_PAGE_KEY. ":" .$slug;
            $redisData = Redis::get($redisKey);

            if (!$redisData){
                $page = $this->fetchPageData($slug);
                Redis::set($redisKey, $page);
                return $this->sendSuccessResponse($page, 'Page data');
            }
            return $this->sendSuccessResponse(json_decode($redisData), 'Page data');
        }catch (\Exception $exception){
            return $this->sendErrorResponse('Data Fetching failed', $exception->getMessage(), 404);
        }
    }

    public function fetchPageData($slug)
    {
        $page = $this->pageRepository->findOneByProperties(
            ['url_slug' => $slug, 'status' => 1],
            ["id", "name", "url_slug", "page_header_en", "page_header_bn", "schema_markup"]
        );

        if(!empty($page->pageComponents)){
            $pageComponents = [];
            foreach ($page->pageComponents as $comIndex => $component){
                $pageComponents[] = $component;
                $componentData = [];

                foreach ($component->componentData as $data){
                    if ($data->parent_id == 0) {
                        $componentData[$data->group][$data->key] = [
                            'en' => $data->value_en,
                            'bn' => $data->value_bn,
                        ];
                    }
                    $tabComponents = [
                        "tab_component_with_image_card_one",
                        "tab_component_with_image_card_two",
                        "tab_component_with_image_card_three",
                        "tab_component_with_image_card_four"
                    ];

                    $tabItemData = [];
                    if (!empty($data->children) && in_array($component->type, $tabComponents)) {
                        foreach ($data->children as $childData) {
                            if ($component->type == "tab_component_with_image_card_four" && $childData->key == "content_type" || $childData->key == "static_component") {
                                $componentData[$data->group][$childData->key] = [
                                    'en' => $childData->value_en,
                                    'bn' => $childData->value_bn,
                                ];
                            }else{
                                $tabItemData["$childData->group"][$childData->key] = [
                                    'en' => $childData->value_en,
                                    'bn' => $childData->value_bn,
                                ];
                            }
                        }
                        if (!empty($tabItemData)){
                            $componentData[$data->group]['items'] = array_values($tabItemData);
                        }
                    }
                }
                unset($component->componentData);
                $pageComponents[$comIndex]['data'] = array_values($componentData);
            }
            $page['page_components'] = $pageComponents;
            return $page;
        }
    }
}

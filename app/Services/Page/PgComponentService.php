<?php

namespace App\Services\Page;

use App\Models\Page\NewPageComponentData;
use App\Repositories\Page\PageRepository;
use App\Repositories\Page\PgComponentDataRepository;
use App\Repositories\Page\PgComponentRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Support\Facades\DB;

class PgComponentService
{
    use CrudTrait;
    use FileTrait;
    private $pageRepository;
    private $componentRepository;
    private $componentDataRepository;

    /**
     * PageService constructor.
     * @param PageRepository $pageRepository
     */
    public function __construct(
        PageRepository $pageRepository,
        PgComponentRepository $componentRepository,
        PgComponentDataRepository $componentDataRepository
    ) {

        $this->componentRepository = $componentRepository;
        $this->pageRepository = $pageRepository;
        $this->componentDataRepository = $componentDataRepository;
        $this->setActionRepository($componentRepository);
    }

    public function storeUpdatePageComponent($data, $id = null)
    {
//        dd($data);
        DB::transaction(function () use ($data, $id) {
            $components = $this->componentRepository->findAll();

            if (isset($data["attribute"]['image_file'])) {
                $imgUrl = $this->fileUpload($data["attribute"]['image_file']);
                $data["attribute"]['image']['en'] = $imgUrl;
                $data["attribute"]['image']['bn'] = $imgUrl;
            }

            if (isset($data["attribute"]['bg_img'])) {
                $imgUrl = $this->fileUpload($data["attribute"]['bg_img']);
                $data["attribute"]['bg_image']['en'] = $imgUrl;
                $data["attribute"]['bg_image']['bn'] = $imgUrl;
            }

            if (isset($data["attribute"])) {
                foreach ($data["attribute"] as $key => $attrItem){
                    if (!is_object($attrItem) && !isset($attrItem['bn'])){
                        $data['attribute'][$key]['bn'] = $attrItem['en'];
                    }
                }
            }

            if ($id && !isset($data["attribute"]['image_file'])) {
                $component = $this->findOne($id);
                if (isset($component['attribute']['image']['en']) && isset($component['attribute']['image']['bn']))
                {
                    $data["attribute"]['image']['en'] = $component['attribute']['image']['en'] ?? null;
                    $data["attribute"]['image']['bn'] = $component['attribute']['image']['bn'] ?? null;
                }
            }

            if ($id && !isset($data["attribute"]['bg_img'])) {
                $component = $this->findOne($id);
                if (isset($component['attribute']['bg_image']['en']) && isset($component['attribute']['bg_image']['bn']))
                {
                    $data["attribute"]['bg_image']['en'] = $component['attribute']['bg_image']['en'] ?? null;
                    $data["attribute"]['bg_image']['bn'] = $component['attribute']['bg_image']['bn'] ?? null;
                }
            }

            $componentData = [
                'page_id' => $data['pageId'],
                'name' => strtoupper(str_replace('_', ' ', $data["component_type"])),
                'type' => $data["component_type"],
                'attribute' => $data["attribute"] ?? null,
                'config' => $data["config"] ?? null,
                'status' => $data['status']
            ];

            if (!$id) {
                $componentData['order'] = $components->count() + 1;
            }

            unset($componentData['attribute']['image_file']);
            unset($componentData['attribute']['bg_img']);
            $componentInfo = $this->componentRepository->createOrUpdate($componentData, $id);
            $componentId = $componentInfo->id;

            if (isset($data['componentData'])){
                foreach (array_values($data['componentData']) as $index => $item) {
                    $tabParentId = 0;

                    // Tab component four condition
                    if (isset($item['content_type']) && $item['content_type']['value_en'] == "static"){
                        unset($item['tab_items']);
                    }

                    foreach ($item as $key => $field) {
                        $valueEn = $field['value_en'] ?? null;
                        $itemEn = is_object($valueEn) ? $this->fileUpload($valueEn) : $valueEn;
                        $tabItemKeys = [
                            'tab_items',
                            'is_static_component',
                            'component_name',
                            'content_type',
                            'static_component'
                        ];
                        if (!in_array($key, $tabItemKeys)) {
                            $componentDataInfo = [
                                'id' => $field['id'] ?? null,
                                'component_id' => $componentId,
                                'parent_id' => 0,
                                'key' => $key,
                                'value_en' => $itemEn,
                                'value_bn' => isset($field['value_bn']) && $field['value_bn'] != null ? $field['value_bn'] : $itemEn,
                                'group' => $index + 1,
                            ];
                            $componentDataSave = $this->componentDataRepository->createOrUpdate($componentDataInfo);
                        }

                        if (isset($field['is_tab'])) {
                            $tabParentId = $componentDataSave->id ?? 0;
                        }

                        if ($key == "content_type" || $key == "static_component"){
                            $valueEn = $field['value_en'] ?? null;
                            $tabItemData = [
                                'id' => $field['id'] ?? null,
                                'component_id' => $componentId,
                                'parent_id' => $tabParentId,
                                'key' => $key,
                                'value_en' => $valueEn,
                                'value_bn' => $valueEn,
                                'group' => $index + 1,
                            ];
                            $this->componentDataRepository->createOrUpdate($tabItemData);
                        }

//                        if ($key == "is_static_component" || $key == "component_name") {
//                            $componentDataInfo = [
//                                'component_id' => $componentId,
//                                'parent_id' => $tabParentId,
//                                'key' => $key,
//                                'value_en' => is_object($valueEn) ? $this->fileUpload($valueEn) : $valueEn,
//                                'value_bn' => $field['value_bn'] ?? null,
//                                'group' => $field['group'] ?? 0,
//                            ];
//                            $componentDataSave = $this->componentDataRepository->save($componentDataInfo);
//                        }
//                        dd($item);
                        if ($key == "tab_items") {
                            foreach ($field as $tabIndex => $tabItems) {
                                foreach ($tabItems as $tabItemKey => $tabItem) {
                                    $valueEn = $tabItem['value_en'] ?? null;
                                    $tabItemData = [
                                        'id' => $tabItem['id'] ?? null,
                                        'component_id' => $componentId,
                                        'parent_id' => $tabParentId,
                                        'key' => $tabItemKey,
                                        'value_en' => is_object($valueEn) ? $this->fileUpload($valueEn) : $valueEn,
                                        'value_bn' => $tabItem['value_bn'] ?? null,
                                        'group' => $tabParentId . "." . ($tabIndex + 1),
                                    ];
                                    $this->componentDataRepository->createOrUpdate($tabItemData);
                                }
                            }
                        }
                    }
                }
            }
        });
    }

    public function fileUpload($file)
    {
        if (is_object($file)){
            return $this->upload($file, 'images/page-component', '');
        }
        return null;
    }

    public function saveSortedData($data)
    {
        if (!empty($data)) {
            foreach ($data['position'] as $item){
                $comId = $item[0];
                $comPosition = $item[1];
                $pageComponent = $this->componentRepository->findOne($comId);
                $pageComponent->order = $comPosition;
                $pageComponent->update();
            }
        }
    }

    public function deleteDataItem($data)
    {
        if ($data['data-parent'] > 0) {
            $componentData = $this->componentDataRepository->findBy(['parent_id' => $data['data-parent'], 'group' => $data['data-group']]);
        } else {
            $componentData = $this->componentDataRepository->findBy(['component_id' => $data['data-com-id'], 'group' => $data['data-group']], 'children');
        }

        foreach ($componentData as $item) {
            if ($item->key == "image" || $item->key == "image_hover") {
                $this->deleteFile($item->value_en);
            }
            if (!empty($item->children)) {
                foreach ($item->children as $data){
                    $data->delete();
                }
            }
            $item->delete();
        }
    }

    public function destroy($id)
    {
        $pageComponent = $this->findOne($id,['componentData' => function ($q){
            $q->where('key', 'image');
        }]);

        // Delete Component Images
        if (!empty($pageComponent->componentData)) {
            foreach ($pageComponent->componentData as $item){
                $this->deleteFile($item->value_en);
            }
        }
        $pageComponent->delete();
    }
}

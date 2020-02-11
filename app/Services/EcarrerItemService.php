<?php

namespace App\Services;

use App\Repositories\EcarrerPortalRepository;
use App\Repositories\EcarrerPortalItemRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Http\Response;
use Carbon\Carbon;

class EcarrerItemService
{
    use CrudTrait;
    use FileTrait;

    /**
     * [$ecarrerPortalItemRepository description]
     * @var [type]
     */
    protected $ecarrerPortalItemRepository;

    /**
     * [$ecarrerPortalRepository description]
     * @var [type]
     */
    protected $ecarrerPortalRepository;


    /**
     * PrizeService constructor.
     * @param PrizeRepository $prizeRepository
     */
    public function __construct(EcarrerPortalItemRepository $ecarrerPortalItemRepository, EcarrerPortalRepository $ecarrerPortalRepository)
    {
        $this->ecarrerPortalItemRepository = $ecarrerPortalItemRepository;
        $this->ecarrerPortalRepository = $ecarrerPortalRepository;
        $this->setActionRepository($ecarrerPortalItemRepository);
    }


    /**
     * store general section parent item on create
     * @param  [type] $request [description]
     * @return [type]          [description]
     */
    public function storeEcarrerItem($data, $parent_id){

        # Life at Banglalink General section
        $data['ecarrer_portals_id'] = $parent_id;

        if (!empty($data['image_url'])) {
            $data['image'] = $this->upload($data['image_url'], 'assetlite/images/ecarrer/general_section');
        }

        $call_to_action_buttons = [];
        if ( isset($data['call_to_action_label_en_1']) && !empty($data['call_to_action_label_en_1']) ) {
            
            if( !empty($data['call_to_action_count'])){

                for ($i=1; $i <= $data['call_to_action_count']; $i++) { 

                    $buttons = [];
                    
                    if( isset($data['call_to_action_label_en_'.$i]) ){
                        $buttons['label_en'] = $data['call_to_action_label_en_'.$i];
                    }

                    if( isset($data['call_to_action_label_bn_'.$i]) ){
                        $buttons['label_bn'] = $data['call_to_action_label_bn_'.$i];
                    }

                    if( isset($data['call_to_action_url_'.$i]) ){
                        $buttons['link'] = $data['call_to_action_url_'.$i];
                    }

                    if( !empty($buttons) ){
                        $call_to_action_buttons['button_'.$i] = $buttons;
                    }

                }
            }
        }

        if( !empty($call_to_action_buttons) ){
            $data['call_to_action'] = serialize($call_to_action_buttons);
        }
        else{
            $data['call_to_action'] = null;
        }

        if( !empty($data['additional_info'])  ){
            $data['additional_info'] = json_encode($data['additional_info']);
        }

        $this->save($data);
        return new Response('eCarrer item created successfully');

    }

    /**
     * Get all items by section id
     * @return [type] [description]
     */
    public function getItems($parent_id){

        return $this->ecarrerPortalItemRepository->getItemsByParentID($parent_id);

    }

    /**
     * [getSingleItemByIds description]
     * @param  [type] $parent_id [description]
     * @param  [type] $id        [description]
     * @return [type]            [description]
     */
    public function getSingleItemByIds($parent_id, $id){

        return $this->ecarrerPortalItemRepository->getSingleItemByID($parent_id, $id);

    }

    /**
     * Get Ecarrer section slug by sectionid
     * @return [type] [description]
     */
    public function getEcarrerSectionSlugByID($section_id){

        return $this->ecarrerPortalRepository->getSectionSlugByID($section_id);

    }

    /**
     * [getEcarrerParentDataByID description]
     * @param  [type] $section_id [description]
     * @return [type]             [description]
     */
    public function getEcarrerParentDataByID($section_id){
        return $this->ecarrerPortalRepository->getSectionDataByID($section_id);
    }


    /**
     * [updateEcarrerGeneralSection description]
     * @param  [type] $data [description]
     * @param  [type] $id   [description]
     * @return [type]       [description]
     */
    public function updateEcarrerItem($data, $id)
    {
        $ecarrer_item = $this->findOne($id);

        if (!empty($data['image_url'])) {
           
            $data['image'] = $this->upload($data['image_url'], 'assetlite/images/ecarrer/general_section');
        }

        $call_to_action_buttons = [];
        if ( isset($data['call_to_action_label_en_1']) && !empty($data['call_to_action_label_en_1']) ) {
            
            if( !empty($data['call_to_action_count'])){

                for ($i=1; $i <= $data['call_to_action_count']; $i++) { 

                    $buttons = [];
                    
                    if( isset($data['call_to_action_label_en_'.$i]) ){
                        $buttons['label_en'] = $data['call_to_action_label_en_'.$i];
                    }

                    if( isset($data['call_to_action_label_bn_'.$i]) ){
                        $buttons['label_bn'] = $data['call_to_action_label_bn_'.$i];
                    }

                    if( isset($data['call_to_action_url_'.$i]) ){
                        $buttons['link'] = $data['call_to_action_url_'.$i];
                    }

                    if( !empty($buttons) ){
                        $call_to_action_buttons['button_'.$i] = $buttons;
                    }

                }
            }
        }

        if( !empty($call_to_action_buttons) ){
            $data['call_to_action'] = serialize($call_to_action_buttons);
        }
        else{
            $data['call_to_action'] = null;
        }

        if( !empty($data['additional_info'])  ){
            $data['additional_info'] = json_encode($data['additional_info']);
        }
        

        $ecarrer_item->update($data);

        return Response('Item updated successfully');
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     * @throws \Exception
     */
    public function deleteItem($id)
    {
        $item = $this->findOne($id);
        $item['deleted_at'] = Carbon::now();
        $item->update();
        return Response('Item deleted successfully !');
    }

    /**
     * [getParentSlug description]
     * @param  [type] $parent_id [description]
     * @return [type]            [description]
     */
    public function getParentRouteSlug($section_id){

        return $this->ecarrerPortalRepository->getParentRouteSlugByID($section_id);

    }

}

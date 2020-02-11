<?php

namespace App\Services;

use App\Repositories\EcarrerPortalRepository;
use App\Repositories\EcarrerPortalItemRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Http\Response;
use Carbon\Carbon;

class EcarrerService
{
    use CrudTrait;
    use FileTrait;

    /**
     * @var $ecarrerPortalService
     */
    protected $ecarrerPortalRepository;

    /**
     * [$ecarrerPortalItemRepository description]
     * @var [type]
     */
    protected $ecarrerPortalItemRepository;

    /**
     * PrizeService constructor.
     * @param PrizeRepository $prizeRepository
     */
    public function __construct(EcarrerPortalRepository $ecarrerPortalRepository, EcarrerPortalItemRepository $ecarrerPortalItemRepository)
    {
        $this->ecarrerPortalRepository = $ecarrerPortalRepository;
        $this->ecarrerPortalItemRepository = $ecarrerPortalItemRepository;
        $this->setActionRepository($ecarrerPortalRepository);
    }


    /**
     * store general section parent item on create
     * @param  [type] $request [description]
     * @return [type]          [description]
     */
    // public function storeEcarrerGeneralSection($data){

    //     # Life at Banglalink General section
    //     $data['category'] = 'life_at_bl_general';
    //     # This section has child item available
    //     $data['has_items'] = 1;

    //     $data['slug'] = str_replace(" ", "_", strtolower($data['slug']));

    //     if (!empty($data['image_url'])) {
    //         $data['image'] = $this->upload($data['image_url'], 'assetlite/images/ecarrer/general_section');
    //     }

    //     $this->save($data);
    //     return new Response('Section created successfully');

    // }

    /**
     * Get all general section for life of banglalink
     * @return [type] [description]
     */
    public function generalSections(){

        return $this->ecarrerPortalRepository->getSectionsByCategory('life_at_bl_general');

    }

    /**
     * General section by ID
     * @return [type] [description]
     */
    public function generalSectionById($id){

        return $this->findOne($id);

    }


    /**
     * [updateEcarrerGeneralSection description]
     * @param  [type] $data [description]
     * @param  [type] $id   [description]
     * @return [type]       [description]
     */
    // public function updateEcarrerGeneralSection($data, $id)
    // {
    //     $general_section = $this->findOne($id);

    //     $data['slug'] = str_replace(" ", "_", strtolower($data['slug']));

    //     if (!empty($data['image_url'])) {
           
    //         $data['image'] = $this->upload($data['image_url'], 'assetlite/images/ecarrer/general_section');
    //     }

    //     $general_section->update($data);

    //     return Response('Section updated successfully');
    // }


    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     * @throws \Exception
     */
    public function sectionDelete($id)
    {
        $section = $this->findOne($id);
        $data['deleted_at'] = Carbon::now();
        $section->update($data);

        $this->ecarrerPortalItemRepository->sectionItemSoftDeleteBySectionID($id);

        return Response('Section deleted successfully !');
    }


    /**
     * Life at bl teams sections
     * @return [type] [description]
     */
    public function ecarrerSectionsList($categoryTypes){

        return $this->ecarrerPortalRepository->getSectionsByCategory($categoryTypes);

    }


    /**
     * store teams section on create
     * @param  [type] $request [description]
     * @return [type]          [description]
     */
    public function storeEcarrerSection($data, $data_types = null){

        # Life at Banglalink General section
        $data['category'] = !empty($data_types['category']) ? $data_types['category'] : null;
        # This section has child item available
        $data['has_items'] = !empty($data_types['has_items']) ? $data_types['has_items'] : 0;
        $data['route_slug'] = !empty($data_types['route_slug']) ? $data_types['route_slug'] : null;
        $data['additional_info'] = !empty($data_types['additional_info']) ? $data_types['additional_info'] : null;

        if( !empty($data['slug']) ){
            $data['slug'] = str_replace(" ", "_", strtolower($data['slug']));
        }
        
        if (!empty($data['image_url'])) {
            $data['image'] = $this->upload($data['image_url'], 'assetlite/images/ecarrer/general_section');
        }

        $this->save($data);
        return new Response('Section created successfully');

    }



    /**
     * [updateEcarrerGeneralSection description]
     * @param  [type] $data [description]
     * @param  [type] $id   [description]
     * @return [type]       [description]
     */
    public function updateEcarrerSection($data, $id, $data_types = null)
    {
        $general_section = $this->findOne($id);

        if( !empty($data['slug']) ){
            $data['slug'] = str_replace(" ", "_", strtolower($data['slug']));
        }
        
        if (!empty($data['image_url'])) {
           
            $data['image'] = $this->upload($data['image_url'], 'assetlite/images/ecarrer/general_section');
        }

        if( isset($data_types['has_items']) ){
            $data['has_items'] = $data_types['has_items'];
        }

        $data['additional_info'] = !empty($data_types['additional_info']) ? $data_types['additional_info'] : null;        

        $general_section->update($data);

        return Response('Section updated successfully');
    }


    /**
     * [getRouteSlug description]
     * @param  [type] $request [description]
     * @return [type]          [description]
     */
    public function getRouteSlug($path){
        if( !empty($path) ){
            $match = explode('/', $path);
            if( !empty($match[0]) && !empty($match[1]) ){
                return $match[0].'/'.$match[1];
            }
            else{
                return null;
            }
        }
        else{
            return null;
        }
        
    }

}

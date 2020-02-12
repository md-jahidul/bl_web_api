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
    public function ecarrerSectionsList($category, $categoryTypes = null){

        return $this->ecarrerPortalRepository->getSectionsByCategory($category, $categoryTypes);

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


   /**
   * Get programs SAP service
   * category
   * => programs_news_section
   * category types
   * => sap
   * Additional types
   * => programs_news_section
   * @return [type] [mixed]
   */
   public function getProgramsSap(){
      

      $results = [];
   
      $results['sap_news_section'] = $this->getProgramsSapNewsSections();

      // $results['sap_news_section'] = $this->getProgramsSapStepsSections();


      return $results;
      

      # Programs Steps sections
      # 
      
      
        
   }


   /**
    * Get programs general section by category type
    * @param  [type] $category [description]
    * @param  [type] $category_type [description]
    * @param  [type] $additional_category [description]
    * Available category types
    * => sap, ennovators, aip
    * Available additional category
    * => programs_news_section, programs_steps, programs_events, programs_testimonial
    * @return [type]                [description]
    */
   private function getProgramsByCateogryType($category, $category_type, $additional_category = null){

      $programs_general = $this->ecarrerSectionsList($category, $category_type);

      $resutls = [];
      if( !empty($programs_general) && count($programs_general) > 0 ){

         if( !empty($additional_category) ){
            foreach ($programs_general as $value) {
               if( !empty($value->additional_info) && json_decode($value->additional_info)->additional_type == $additional_category ){

                  $resutls[] =  $value; 

               }
               
            }

         }
         else{
            $resutls = $programs_general;
         }

      }

      return $resutls;

   }


   /**
    * Programs SAP news sections
    * @return [type] [description]
    */
   private function getProgramsSapNewsSections(){

      # Ecarrer programs news section
      $get_sap_news = $this->getProgramsByCateogryType('programs_progeneral', 'sap', 'programs_news_section');

      $sub_data = [];
      if( !empty($get_sap_news) ){
         foreach ($get_sap_news as $value) {

            if( !empty($value->portalItems) && count($value->portalItems) > 0 ){
               foreach ($value->portalItems as $items_value){
                  $sub_data['title_en'] = $items_value->title_en;
                  $sub_data['title_bn'] = $items_value->title_bn;
                  $sub_data['description_en'] = $items_value->description_bn;
                  $sub_data['description_bn'] = $items_value->description_bn;

                  $sub_data['image'] = !empty($items_value->image) ? config('filesystems.image_host_url') . $items_value->image : null;

                  $sub_data['alt_text'] = $items_value->alt_text;
                  $sub_data['alt_links'] = $items_value->alt_links;

                  #teams tab content buttons
                  $sub_data['call_to_action_buttons'] = !empty($items_value->call_to_action) ? unserialize($items_value->call_to_action) : null;
               }
            }

         }

      }

      return $sub_data;

   }

   /**
    * [getProgramsSapStepsSections description]
    * @return [type] [description]
    */
   public function getProgramsSapStepsSections(){

      $get_sap_news = $this->getProgramsByCateogryType('programs_progeneral', 'sap', 'programs_steps');

      $sub_data = [];

      foreach ($life_at_bl_events as $events_value) {

         $sub_data = [];
         $sub_data['title_en'] = $events_value->title_en; 
         $sub_data['title_bn'] = $events_value->title_bn; 
         $sub_data['slug'] = $events_value->slug; 
         if( !empty($events_value->additional_info) ){
            $sub_data['sider_info'] = json_decode($events_value->additional_info)->sider_info;
         }

         if( !empty($events_value->portalItems) ){

            foreach ($events_value->portalItems as $portal_items) {
               $sub_items = [];

               $sub_items['title_en'] = $portal_items->title_en;
               $sub_items['image'] = !empty($portal_items->image) ? config('filesystems.image_host_url') . $portal_items->image : null;
               $sub_items['alt_text'] = $portal_items->alt_text;

               $sub_data['item_list'][] = $sub_items;

            }

         }

         $data['events_activites'] = $sub_data;


      } // Foreach end


      return $results;

   }


   /**
    * Get Vacancy Hire section
    * @return [type] [description]
    */
   public function getVacancyHire(){

      $vacancy_hire = $this->getProgramsByCateogryType('vacancy_pioneer', 'how_we_hire');
      $results = [];
      if( !empty($vacancy_hire) && count($vacancy_hire) > 0 ){
         foreach ($vacancy_hire as $parent_value) {

            $sub_data = [];
            $sub_data['title_en'] = $parent_value->title_en; 
            $sub_data['title_bn'] = $parent_value->title_bn; 
            $sub_data['slug'] = $parent_value->slug; 
            $sub_data['description_en'] = $parent_value->description_en; 
            $sub_data['description_bn'] = $parent_value->description_bn;
            $sub_data['image'] = !empty($parent_value->image) ? config('filesystems.image_host_url') . $parent_value->image : null;
            $sub_data['alt_text'] = $parent_value->alt_text;
            

            // if( !empty($events_value->portalItems) ){

            //    foreach ($events_value->portalItems as $portal_items) {
            //       $sub_items = [];

            //       $sub_items['title_en'] = $portal_items->title_en;
            //       $sub_items['image'] = !empty($portal_items->image) ? config('filesystems.image_host_url') . $portal_items->image : null;
            //       $sub_items['alt_text'] = $portal_items->alt_text;

            //       $sub_data['item_list'][] = $sub_items;

            //    }

            // }

            $results = $sub_data;


         } // Foreach end
      }

      return $results;

   }

   /**
    * Get vacancy bottom news media section
    * @return [type] [description]
    */
   public function getVacancyNewsMedia(){

      $vacancy_news_media = $this->getProgramsByCateogryType('vacancy_pioneer', 'bottom_news_media');
      $results = [];
      if( !empty($vacancy_news_media) && count($vacancy_news_media) > 0 ){
         foreach ($vacancy_news_media as $parent_value) {

            $sub_data = [];
            $sub_data['title_en'] = $parent_value->title_en; 
            $sub_data['title_bn'] = $parent_value->title_bn; 
            $sub_data['slug'] = $parent_value->slug; 
            $sub_data['description_en'] = $parent_value->description_en; 
            $sub_data['description_bn'] = $parent_value->description_bn;
            // $sub_data['image'] = !empty($parent_value->image) ? config('filesystems.image_host_url') . $parent_value->image : null;
            $sub_data['video'] = $parent_value->video;

            $results = $sub_data;


         } // Foreach end
      }

      return $results;

   }

   /**
    * Get ecarrer vacancy box icons
    * @return [type] [description]
    */
   public function getVacancyBoxIcon(){

      $vacancy_news_media = $this->ecarrerSectionsList('vacancy_viconbox');
      
      $results = [];
      if( !empty($vacancy_news_media) && count($vacancy_news_media) > 0 ){
         foreach ($vacancy_news_media as $parent_value) {

            $sub_data = [];
            $sub_data['title_en'] = $parent_value->title_en; 
            $sub_data['title_bn'] = $parent_value->title_bn; 
            $sub_data['slug'] = $parent_value->slug; 
            $sub_data['description_en'] = $parent_value->description_en; 
            $sub_data['description_bn'] = $parent_value->description_bn;
            $sub_data['image'] = !empty($parent_value->image) ? config('filesystems.image_host_url') . $parent_value->image : null;
            $sub_data['alt_text'] = $parent_value->alt_text;

            $results[] = $sub_data;


         } // Foreach end
      }

      return $results;

   }

   /**
    * Ecarrer vacancy job offers with lever api
    * @return [type] [description]
    */
   public function getVacancyJobOffers(){

   }



}


   
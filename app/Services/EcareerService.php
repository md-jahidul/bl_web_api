<?php

namespace App\Services;

use App\Repositories\EcareerPortalRepository;
use App\Repositories\EcareerPortalItemRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Http\Response;
use Carbon\Carbon;
use App\Enums\HttpStatusCode;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use App\Models\University;
use App\Models\EcareerPortalForm;
use Illuminate\Database\QueryException;
use File;

class EcareerService
{

    use CrudTrait;
    use FileTrait;

    # GuzzleHttp\Exception\ClientException for 400-level errors
    # GuzzleHttp\Exception\ServerException for 500-level errors
    # GuzzleHttp\Exception\BadResponseException for both (it's their superclass)

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
    public function __construct(EcareerPortalRepository $ecarrerPortalRepository, EcareerPortalItemRepository $ecarrerPortalItemRepository)
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
    public function generalSections()
    {

        return $this->ecarrerPortalRepository->getSectionsByCategory('life_at_bl_general');
    }

    /**
     * General section by ID
     * @return [type] [description]
     */
    public function generalSectionById($id)
    {

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

    public function getSeoData($category)
    {
        return $this->ecarrerPortalRepository->getSeoData($category);
    }

    /**
     * Life at bl teams sections
     * @return [type] [description]
     */
    public function ecarrerSectionsList($category, $categoryTypes = null)
    {

        return $this->ecarrerPortalRepository->getSectionsByCategory($category, $categoryTypes);
    }

    /**
     * store teams section on create
     * @param  [type] $request [description]
     * @return [type]          [description]
     */
    public function storeEcarrerSection($data, $data_types = null)
    {

        # Life at Banglalink General section
        $data['category'] = !empty($data_types['category']) ? $data_types['category'] : null;
        # This section has child item available
        $data['has_items'] = !empty($data_types['has_items']) ? $data_types['has_items'] : 0;
        $data['route_slug'] = !empty($data_types['route_slug']) ? $data_types['route_slug'] : null;
        $data['additional_info'] = !empty($data_types['additional_info']) ? $data_types['additional_info'] : null;

        if (!empty($data['slug'])) {
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

        if (!empty($data['slug'])) {
            $data['slug'] = str_replace(" ", "_", strtolower($data['slug']));
        }

        if (!empty($data['image_url'])) {

            $data['image'] = $this->upload($data['image_url'], 'assetlite/images/ecarrer/general_section');
        }

        if (isset($data_types['has_items'])) {
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
    public function getRouteSlug($path)
    {
        if (!empty($path)) {
            $match = explode('/', $path);
            if (!empty($match[0]) && !empty($match[1])) {
                return $match[0] . '/' . $match[1];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function programsTapSeoData($catType)
    {
        return $this->ecarrerPortalRepository->findOneByProperties(
            [
                'category' => 'programs_top_tab_title',
                'category_type' => $catType
            ]
        );
    }

    /**
     * Get programs SAP service sections
     * category
     * => programs_top_tab_title
     * => programs_progeneral
     * => programs_proiconbox
     * => programs_photogallery
     * => programs_sapbatches
     * category types
     * => sap
     * Additional types
     * => programs_news_section
     * @return [type] [mixed]
     */
    public function getProgramsSap()
    {


        $results = null;

        try {

            $category = "sap";
            $seoData = $this->programsTapSeoData($category);


//            dd($seoData);

            $results['seo_data'] = array(
                'banner_web' => $seoData->image == "" ? "" : config('filesystems.image_host_url') . $seoData->image,
                'banner_mobile' => $seoData->image_mobile == "" ? "" : config('filesystems.image_host_url') . $seoData->image_mobile,
                'alt_text' => $seoData->alt_text,
                'page_header' => $seoData->page_header,
                'page_header_bn' => $seoData->page_header_bn,
                'schema_markup' => $seoData->schema_markup
            );

            # get sap title for tab
            $results['tab_title'] = $this->getProgramsTabTitle('programs_top_tab_title', 'sap');
            $sections['news_section'] = $this->getProgramsNewsSections('programs_progeneral', 'sap', 'programs_news_section');
            $sections['steps_section'] = $this->getProgramsStepsSections('programs_progeneral', 'sap', 'programs_steps');
            $sections['boxicon_section'] = $this->getProgramsBoxIconSections('programs_proiconbox', 'sap');
            $sections['photogallery_section'] = $this->getProgramsPhotoGallerySections('programs_photogallery', 'sap');
            $sections['previousbatch_section'] = $this->getProgramsPreviousBatchSections('programs_sapbatches');

            $results['sections'] = $sections;


            return $results;
        } catch (\Exception $e) {
            return $results;
        };
    }

    /**
     * Get programs SAP service sections
     * category
     * => programs_top_tab_title
     * => programs_progeneral
     * => programs_proiconbox
     * => programs_photogallery
     * => programs_sapbatches
     * category types
     * => ennovators
     * Additional types
     * => programs_news_section
     * => programs_steps
     * => programs_events
     * @return [type] [mixed]
     */
    public function getProgramsEnnovators()
    {


        $results = null;

        try {

            $category = "ennovators";
            $seoData = $this->programsTapSeoData($category);

            $results['seo_data'] = array(
                'banner_web' => $seoData->image == "" ? "" : config('filesystems.image_host_url') . $seoData->image,
                'banner_mobile' => $seoData->image_mobile == "" ? "" : config('filesystems.image_host_url') . $seoData->image_mobile,
                'alt_text' => $seoData->alt_text,
                'page_header' => $seoData->page_header,
                'page_header_bn' => $seoData->page_header_bn,
                'schema_markup' => $seoData->schema_markup
            );

            # get sap title for tab
            $results['tab_title'] = $this->getProgramsTabTitle('programs_top_tab_title', 'ennovators');
            $sections['news_section'] = $this->getProgramsNewsSections('programs_progeneral', 'ennovators', 'programs_news_section');
            $sections['boxicon_section'] = $this->getProgramsBoxIconSections('programs_proiconbox', 'ennovators');
            $sections['steps_section'] = $this->getProgramsStepsSections('programs_progeneral', 'ennovators', 'programs_steps');
            $sections['previousbatch_section'] = $this->getProgramsPreviousBatchSections('programs_ennovatorbatches');
            $sections['programs_events'] = $this->getProgramsEventsSections('programs_progeneral', 'ennovators', 'programs_events');
            $sections['photogallery_section'] = $this->getProgramsPhotoGallerySections('programs_photogallery', 'ennovators');


            $results['sections'] = $sections;


            return $results;
        } catch (\Exception $e) {
            return $results;
        };
    }

    /**
     * Get programs SAP service sections
     * category
     * => programs_top_tab_title
     * => programs_progeneral
     * => programs_proiconbox
     * => programs_photogallery
     * => programs_sapbatches
     * category types
     * => sap
     * Additional types
     * => programs_news_section
     * @return [type] [mixed]
     */
    public function getProgramsAip()
    {
        $results = null;

        try {

            $category = "aip";
            $seoData = $this->programsTapSeoData($category);

            $results['seo_data'] = array(
                'banner_web' => $seoData->image == "" ? "" : config('filesystems.image_host_url') . $seoData->image,
                'banner_mobile' => $seoData->image_mobile == "" ? "" : config('filesystems.image_host_url') . $seoData->image_mobile,
                'alt_text' => $seoData->alt_text,
                'page_header' => $seoData->page_header,
                'page_header_bn' => $seoData->page_header_bn,
                'schema_markup' => $seoData->schema_markup
            );

            # get sap title for tab
            $results['tab_title'] = $this->getProgramsTabTitle('programs_top_tab_title', 'aip');
            $sections['news_section'] = $this->getProgramsNewsSections('programs_progeneral', 'aip', 'programs_news_section');
            $sections['boxicon_section'] = $this->getProgramsBoxIconSections('programs_proiconbox', 'aip');
            $sections['steps_section'] = $this->getProgramsStepsSections('programs_progeneral', 'aip', 'programs_steps');

            $sections['programs_testimonial'] = $this->getProgramsTestimonialSections('programs_progeneral', 'aip', 'programs_testimonial');

            $sections['programs_events'] = $this->getProgramsEventsSections('programs_progeneral', 'aip', 'programs_events');
            $sections['photogallery_section'] = $this->getProgramsPhotoGallerySections('programs_photogallery', 'aip');


            $results['sections'] = $sections;


            return $results;
        } catch (\Exception $e) {
            return $results;
        };
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
    private function getProgramsByCateogryType($category, $category_type, $additional_category = null)
    {

        $programs_general = $this->ecarrerSectionsList($category, $category_type);

        $resutls = [];
        if (!empty($programs_general) && count($programs_general) > 0) {

            if (!empty($additional_category)) {
                foreach ($programs_general as $value) {
                    if (!empty($value->additional_info) && json_decode($value->additional_info)->additional_type == $additional_category) {

                        $resutls[] = $value;
                    }
                }
            } else {
                $resutls = $programs_general;
            }
        }

        return $resutls;
    }

    /**
     * Programs SAP news sections
     * @return [type] [description]
     */
    private function getProgramsNewsSections($category, $category_type, $additional_category)
    {

        $sub_data = null;

        if (empty($category) || empty($category_type) || empty($additional_category)) {
            return $sub_data;
        }

        # Ecarrer programs news section
        $get_sap_news = $this->getProgramsByCateogryType($category, $category_type, $additional_category);

        if (!empty($get_sap_news)) {
            foreach ($get_sap_news as $value) {

                if (!empty($value->portalItems) && count($value->portalItems) > 0) {
                    foreach ($value->portalItems as $items_value) {
                        $sub_data['title_en'] = $items_value->title_en;
                        $sub_data['title_bn'] = $items_value->title_bn;
                        $sub_data['description_en'] = $items_value->description_en;
                        $sub_data['description_bn'] = $items_value->description_bn;

                        $sub_data['image'] = !empty($items_value->image) ? config('filesystems.image_host_url') . $items_value->image : null;
                        $sub_data['video'] = $items_value->video;
                        $sub_data['alt_text'] = $items_value->alt_text;
                        // $sub_data['alt_links'] = $items_value->alt_links;
                        #teams tab content buttons
//                        $sub_data['call_to_action_buttons'] = null;
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
    public function getProgramsStepsSections($category, $category_type, $additional_category)
    {

        $results = null;
        $get_pro_steps = $this->getProgramsByCateogryType($category, $category_type, $additional_category);

        if (empty($category) || empty($category_type) || empty($additional_category)) {
            return $results;
        }


        if (!empty($get_pro_steps) && count($get_pro_steps) > 0) {
            foreach ($get_pro_steps as $parents_value) {

                $sub_data = [];
                $sub_data['title_en'] = $parents_value->title_en;
                $sub_data['title_bn'] = $parents_value->title_bn;
                $sub_data['slug'] = $parents_value->slug;


                if (!empty($parents_value->portalItems)) {

                    foreach ($parents_value->portalItems as $portal_items) {
                        $sub_items = [];

                        // $sub_items['title_en'] = $portal_items->title_en;
                        $sub_items['image'] = !empty($portal_items->image) ? config('filesystems.image_host_url') . $portal_items->image : null;
                        $sub_items['alt_text'] = $portal_items->alt_text;

                        $sub_data['item_list'][] = $sub_items;
                    }
                }

                $results = $sub_data;
            } // Foreach end
        }


        return $results;
    }

    /**
     * [getProgramsSapStepsSections description]
     * @return [type] [description]
     */
    public function getProgramsPhotoGallerySections($category, $category_type)
    {

        $results = null;

        if (empty($category) || empty($category_type)) {
            return $results;
        }

        $programs_photo_gal = $this->getProgramsByCateogryType($category, $category_type);


        if (!empty($programs_photo_gal) && count($programs_photo_gal) > 0) {
            foreach ($programs_photo_gal as $parents_value) {

                $sub_data = [];
                $sub_data['title_en'] = $parents_value->title_en;
                $sub_data['title_bn'] = $parents_value->title_bn;
                $sub_data['slug'] = $parents_value->slug;
                if (!empty($parents_value->additional_info)) {
                    $sub_data['sider_info'] = json_decode($parents_value->additional_info)->sider_info;
                }

                if (!empty($parents_value->portalItems)) {

                    foreach ($parents_value->portalItems as $portal_items) {
                        $sub_items = [];

                        // $sub_items['title_en'] = $portal_items->title_en;
                        $sub_items['image'] = !empty($portal_items->image) ? config('filesystems.image_host_url') . $portal_items->image : null;
                        $sub_items['alt_text'] = $portal_items->alt_text;
                        $sub_items['video'] = $portal_items->video;
                        $sub_data['item_list'][] = $sub_items;
                    }
                }

                $results = $sub_data;
            } // Foreach end
        }


        return $results;
    }

    /**
     * Programs box icon sections
     * @return [type] [description]
     */
    public function getProgramsBoxIconSections($category, $category_type)
    {

        $results = null;
        $programs_proiconbox = $this->ecarrerSectionsList($category, $category_type);

        if (empty($category) || empty($category_type)) {
            return $results;
        }


        if (!empty($programs_proiconbox) && count($programs_proiconbox) > 0) {
            foreach ($programs_proiconbox as $parent_value) {

                $sub_data = [];
                // $sub_data['title_en'] = $parent_value->title_en;
                // $sub_data['title_bn'] = $parent_value->title_bn;
                $sub_data['slug'] = $parent_value->slug;
                // $sub_data['description_en'] = $parent_value->description_en;
                // $sub_data['description_bn'] = $parent_value->description_bn;
                // $sub_data['image'] = !empty($parent_value->image) ? config('filesystems.image_host_url') . $parent_value->image : null;
                // $sub_data['alt_text'] = $parent_value->alt_text;
                if (!empty($parent_value->portalItems)) {

                    foreach ($parent_value->portalItems as $portal_items) {
                        $sub_items = [];

                        $sub_items['title_en'] = $portal_items->title_en;
                        $sub_items['title_bn'] = $portal_items->title_bn;
                        $sub_items['description_en'] = $portal_items->description_en;
                        $sub_items['description_bn'] = $portal_items->description_bn;
                        $sub_items['image'] = !empty($portal_items->image) ? config('filesystems.image_host_url') . $portal_items->image : null;
                        $sub_items['alt_text'] = $portal_items->alt_text;

                        $sub_data['item_list'][] = $sub_items;
                    }
                }

                $results = $sub_data;
            } // Foreach end
        }

        return $results;
    }

    /**
     * Get Vacancy Hire section
     * @return [type] [description]
     */
    public function getVacancyHire()
    {

        $vacancy_hire = $this->getProgramsByCateogryType('vacancy_pioneer', 'how_we_hire');
        $results = [];
        if (!empty($vacancy_hire) && count($vacancy_hire) > 0) {
            foreach ($vacancy_hire as $parent_value) {

                $sub_data = [];
                $sub_data['title_en'] = $parent_value->title_en;
                $sub_data['title_bn'] = $parent_value->title_bn;
                $sub_data['slug'] = $parent_value->slug;
                $sub_data['description_en'] = $parent_value->description_en;
                $sub_data['description_bn'] = $parent_value->description_bn;
                $sub_data['image'] = !empty($parent_value->image) ? config('filesystems.image_host_url') . $parent_value->image : null;
                $sub_data['alt_text'] = $parent_value->alt_text;
                $results = $sub_data;
            } // Foreach end
        }

        return $results;
    }

    /**
     * Get vacancy bottom news media section
     * @return [type] [description]
     */
    public function getVacancyNewsMedia()
    {

        $vacancy_news_media = $this->getProgramsByCateogryType('vacancy_pioneer', 'bottom_news_media');
        $results = [];
        if (!empty($vacancy_news_media) && count($vacancy_news_media) > 0) {
            foreach ($vacancy_news_media as $parent_value) {

                $sub_data = [];
                $sub_data['title_en'] = $parent_value->title_en;
                $sub_data['title_bn'] = $parent_value->title_bn;
                $sub_data['slug'] = $parent_value->slug;
                $sub_data['description_en'] = $parent_value->description_en;
                $sub_data['description_bn'] = $parent_value->description_bn;
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
    public function getVacancyBoxIcon()
    {

        $vacancy_news_media = $this->ecarrerSectionsList('vacancy_viconbox');

        $results = null;
        if (!empty($vacancy_news_media) && count($vacancy_news_media) > 0) {
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
    public function getVacancyLeverJobOffers()
    {

        $results = null;

        $categoryJson = $this->loadJSON();


        # get job offer titles
        $vacancy_job_offer_title = $this->getProgramsByCateogryType('vacancy_pioneer', 'job_offers_title');
        $job_offer_title = [];
        if (!empty($vacancy_job_offer_title) && count($vacancy_job_offer_title) > 0) {
            foreach ($vacancy_job_offer_title as $parent_value) {

                $sub_data = [];
                $sub_data['title_en'] = $parent_value->title_en;
                $sub_data['title_bn'] = $parent_value->title_bn;
                $sub_data['slug'] = $parent_value->slug;

                $job_offer_title = $sub_data;
            } // Foreach end
        } else {
            $job_offer_title = null;
        }

        $results['job_offers_title'] = $job_offer_title;

        # end job offer title
        # Get vacancy job offers from liver api
        # LEVER API:
        # Use this one: https://api.lever.co/v0/postings/vimpelcom/?skip=1&limit=3&mode=json
        #  (No key required)
        #  Otherwise:
        # https://api.lever.co/v0/postings/lever to get paginated results from the API
        # For Demo:
        # https://api.lever.co/v0/postings/leverdemo?skip=1&limit=3&mode=json

        $lever_content = [];
        $jobData = [];
        $client = new Client();
        try {
            $response = $client->get(
                config('apiurl.lever_api_host') . '/postings/' . config('apiurl.lever_api_client') . '/?skip=0&mode=json'
            );

            if ($response->getStatusCode() == HttpStatusCode::SUCCESS) {

                $response = json_decode($response->getBody()->getContents(), true);
                foreach ($categoryJson as $k => $cats) {

                    $jobData[$k]['tabName'] = $cats->catName;
                    foreach ($response as $val) {

                        if (isset($val['categories']['department']) && $val['categories']['department'] == "BANGLALINK") {
                            $depArray = (array)$cats->departments;
                            if (isset($val['categories']['team']) && in_array($val['categories']['team'], $depArray)) {
                                unset($val['additional']);
                                unset($val['description']);
                                unset($val['descriptionPlain']);
                                $val['additionalPlain'] = substr($val['additionalPlain'], 0, 250);
                                $jobData[$k]['jobs'][] = $val;
                            }
                        }
                    }
                }
            }
        } catch (BadResponseException $e) {
            // $response = $e->getResponse();
            $lever_content = null;
            \Log::channel('lever_api_log')->info('Lever api not responding. Client error or Server error');
        } catch (\Exception $e) {
            $lever_content = null;
            \Log::channel('lever_api_log')->info('Technical error when lever api parsing data');
        };

        $results['job_offers_content'] = $jobData;
        # lever api end


        return $results;
    }

    public function loadJSON()
    {

        try {
            $path = public_path() . "/config-json/job-categories.json"; // ie: /var/www/laravel/app/storage/json/filename.json

            if (!File::exists($path)) {
                throw new \Exception("Invalid File");
            }

            $file = File::get($path); // string
            return json_decode($file);
        } catch (\Exception $e) {
            return $e;
        };
    }

    /**
     * Get batch title
     * @return [type] [description]
     */
    private function getProgramsPreviousBatchSections($category)
    {
        $results = null;
        if (empty($category)) {
            return $results;
        }
        # Get Batch main title
        $batch_title = $this->getProgramsByCateogryType($category, 'batch_title');

        if (!empty($batch_title) && count($batch_title) > 0) {
            $sub_data['title_en'] = $batch_title->first()->title_en;
            $sub_data['title_bn'] = $batch_title->first()->title_bn;
            $sub_data['slug'] = $batch_title->first()->slug;
            $results['batch_main_title'] = $sub_data;
        }
        # batch main title end
        # batch tab content
        $programs_batch_content = $this->ecarrerSectionsList($category, 'batch_content');

        if (!empty($programs_batch_content) && count($programs_batch_content) > 0) {
            foreach ($programs_batch_content as $parent_value) {

                $sub_data = [];
                $sub_data['title_en'] = $parent_value->title_en;
                $sub_data['title_bn'] = $parent_value->title_bn;
                $sub_data['slug'] = $parent_value->slug;
                if (!empty($parent_value->portalItems) && count($parent_value->portalItems) > 0) {

                    foreach ($parent_value->portalItems as $portal_items) {
                        $sub_items = [];
                        $sub_items['title_en'] = $portal_items->title_en;
                        $sub_items['title_bn'] = $portal_items->title_bn;
                        $sub_items['description_en'] = $portal_items->description_en;
                        $sub_items['description_bn'] = $portal_items->description_bn;
                        $sub_items['image'] = !empty($portal_items->image) ? config('filesystems.image_host_url') . $portal_items->image : null;
                        $sub_items['alt_text'] = $portal_items->alt_text;

                        if (isset($portal_items->additional_info) && !empty($portal_items->additional_info)) {
                            $additional_data = json_decode($portal_items->additional_info);

                            $sub_items['university_en'] = isset($additional_data->testimonial_info->university_en) ? $additional_data->testimonial_info->university_en : null;
                            $sub_items['university_bn'] = isset($additional_data->testimonial_info->university_bn) ? $additional_data->testimonial_info->university_bn : null;
                        }

                        $sub_data['item_list'][] = $sub_items;
                    }
                } else {
                    $sub_data['item_list'] = null;
                }

                $results['batch_content'][] = $sub_data;
            } // Foreach end
        }

        return $results;
    }

    /**
     * Programs tab tile
     * @return [type] [description]
     */
    private function getProgramsTabTitle($category, $category_type)
    {

        $results = null;

        if (empty($category) || empty($category_type)) {
            return $results;
        }

        $tab_title = $this->ecarrerSectionsList($category, $category_type);

        if (!empty($tab_title) && count($tab_title) > 0) {

            $sub_data['title_en'] = $tab_title->first()->title_en;
            $sub_data['title_bn'] = $tab_title->first()->title_bn;
            $sub_data['slug'] = $tab_title->first()->slug;
            $sub_data['url_slug'] = $tab_title->first()->route_slug;
            $sub_data['url_slug_bn'] = $tab_title->first()->route_slug_bn;

            $results = $sub_data;
        }

        return $results;
    }

    /**
     * Programs SAP news sections
     * @return [type] [description]
     */
    private function getProgramsEventsSections($category, $category_type, $additional_category)
    {

        $results = null;

        if (empty($category) || empty($category_type) || empty($additional_category)) {
            return $results;
        }

        # Ecarrer programs news section
        $get_sap_news = $this->getProgramsByCateogryType($category, $category_type, $additional_category);

        if (!empty($get_sap_news) && count($get_sap_news) > 0) {
            foreach ($get_sap_news as $parent_value) {

                $sub_data = [];
                $sub_data['title_en'] = $parent_value->title_en;
                $sub_data['title_bn'] = $parent_value->title_bn;
                $sub_data['slug'] = $parent_value->slug;

                if (!empty($parent_value->portalItems) && count($parent_value->portalItems) > 0) {
                    foreach ($parent_value->portalItems as $items_value) {

                        $sub_items = [];
                        $sub_items['title_en'] = $items_value->title_en;
                        $sub_items['title_bn'] = $items_value->title_bn;
                        $sub_items['description_en'] = $items_value->description_en;
                        $sub_items['description_bn'] = $items_value->description_bn;

                        $sub_items['image'] = !empty($items_value->image) ? config('filesystems.image_host_url') . $items_value->image : null;
                        $sub_items['alt_text'] = $items_value->alt_text;
                        $sub_items['video'] = $items_value->video;

                        #teams tab content buttons
                        $sub_data['item_list'][] = $sub_items;
                    }
                }

                $results = $sub_data;
            } // endforeach
        }

        return $results;
    }

    /**
     * Programs AIP Testimonial Sections
     * @return [type] [description]
     */
    private function getProgramsTestimonialSections($category, $category_type, $additional_category)
    {

        $results = null;

        if (empty($category) || empty($category_type) || empty($additional_category)) {
            return $results;
        }

        # Ecarrer programs news section
        $get_sap_news = $this->getProgramsByCateogryType($category, $category_type, $additional_category);

        if (!empty($get_sap_news) && count($get_sap_news) > 0) {
            foreach ($get_sap_news as $parent_value) {

                $sub_data = [];
                $sub_data['title_en'] = $parent_value->title_en;
                $sub_data['title_bn'] = $parent_value->title_bn;
                $sub_data['slug'] = $parent_value->slug;

                if (!empty($parent_value->portalItems) && count($parent_value->portalItems) > 0) {
                    foreach ($parent_value->portalItems as $portal_items) {

                        $sub_items = [];
                        $sub_items['title_en'] = $portal_items->title_en;
                        $sub_items['title_bn'] = $portal_items->title_bn;
                        $sub_items['description_en'] = $portal_items->description_en;
                        $sub_items['description_bn'] = $portal_items->description_bn;

                        $sub_items['image'] = !empty($portal_items->image) ? config('filesystems.image_host_url') . $portal_items->image : null;

                        $sub_items['alt_text'] = $portal_items->alt_text;
                        // $sub_items['alt_links'] = $portal_items->alt_links;
                        #university names
                        if (isset($portal_items->additional_info) && !empty($portal_items->additional_info)) {
                            $additional_data = json_decode($portal_items->additional_info);

                            $sub_items['university_en'] = isset($additional_data->testimonial_info->university_en) ? $additional_data->testimonial_info->university_en : null;
                            $sub_items['university_bn'] = isset($additional_data->testimonial_info->university_bn) ? $additional_data->testimonial_info->university_bn : null;
                        }

                        $sub_data['item_list'][] = $sub_items;
                    }
                }

                $results = $sub_data;
            } // endforeach
        }

        return $results;
    }

    /**
     * Get university list
     * @return [type] [description]
     */
    public function getUniversityList()
    {

        return University::get();
    }

    /**
     * Update application form
     * @param  [type] $request [description]
     * @return [type]          [description]
     */
    public function updateApplicationForm($request)
    {


        try {
            $data = null;

            $portal_forms = new EcareerPortalForm;

            $portal_forms->name = isset($request['name']) ? $request['name'] : null;
            $portal_forms->phone = isset($request['phone']) ? $request['phone'] : null;
            $portal_forms->email = isset($request['email']) ? $request['email'] : null;
            $portal_forms->university_id = isset($request['university_id']) ? $request['university_id'] : null;
            $portal_forms->versity_id = isset($request['versity_id']) ? $request['versity_id'] : null;

            if (!empty($request['applicant_cv'])) {
                $portal_forms->applicant_cv = $this->upload($request['applicant_cv'], 'assetlite/images/ecarrer/applicant_files');
            }

            $portal_forms->versity_id = isset($request['versity_id']) ? $request['versity_id'] : null;
            $portal_forms->gender = isset($request['gender']) ? $request['gender'] : null;
            $portal_forms->date_of_birth = isset($request['dob']) ? $request['dob'] : null;
            $portal_forms->cgpa = isset($request['cgpa']) ? $request['cgpa'] : null;
            $portal_forms->address = isset($request['address']) ? $request['address'] : null;

            $portal_forms->save();

            return true;
        } catch (QueryException $e) {
            return response()->json((['status' => 'FAIL', 'status_code' => HttpStatusCode::VALIDATION_ERROR, 'message' => $e->getMessage(), 'errors' => []]), HttpStatusCode::VALIDATION_ERROR);
        }
    }

    /**
     * Programs tab tile
     * @return [type] [description]
     */
    public function getProgramsAllTabTitle($category, $category_type = null, $single = false)
    {

        $results = null;

        if (empty($category)) {
            return $results;
        }

        $tab_titles = $this->ecarrerSectionsList($category, $category_type);

        if (!$single) {
            if (!empty($tab_titles) && count($tab_titles) > 0) {

                foreach ($tab_titles as $tab_title) {
                    $sub_data['url_slug'] = $tab_title->route_slug;
                    $sub_data['slug'] = $tab_title->slug;
                    $results[] = $sub_data;
                }
            }
        } else {
            $results = isset($tab_titles->first()->route_slug) ? $tab_titles->first()->route_slug : null;
        }


        return $results;
    }

}

// Class end



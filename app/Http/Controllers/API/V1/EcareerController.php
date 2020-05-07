<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\EcareerService;
use App\Http\Controllers\API\V1\ConfigController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Enums\HttpStatusCode;

class EcareerController extends Controller {
    /**
     * Available eCarrer portals category
     * # life_at_bl_general
     * # life_at_bl_teams
     * # life_at_bl_events
     * # life_at_bl_diversity
     * # life_at_bl_topbanner
     * # life_at_bl_contact
     * # vacancy_pioneer
     * # vacancy_viconbox
     * # programs_progeneral
     * # programs_proiconbox
     * # programs_photogallery
     * # programs_sapbatches
     * # programs_ennovatorbatches
     */

    /**
     * ecarrer service
     * @var [type]
     */
    private $ecarrerService;

    public function __construct(EcareerService $ecarrerService) {
        $this->ecarrerService = $ecarrerService;
    }

    /**
     * eCarrer top banner and footer contact section api
     * @return [type] [description]
     */
    public function topBannerContact() {

        try {
            $data = [];
            // Top banner menu
            $top_banners = $this->ecarrerService->ecarrerSectionsList('life_at_bl_topbanner');
            if (!empty($top_banners)) {
                foreach ($top_banners as $key => $value) {
                    $sub_data_banner = [];
                    $sub_data_banner['title_en'] = $value->title_en;
                    $sub_data_banner['title_bn'] = $value->title_bn;
                    $sub_data_banner['slug'] = $value->slug;

                    $sub_data_banner['image'] = !empty($value->image) ? config('filesystems.image_host_url') . $value->image : null;
                    $sub_data_banner['alt_text'] = $value->alt_text;

                    $data['top_menu_banner'][] = $sub_data_banner;
                }
            }


            // eCarrer Footer Contact us
            $ecarrer_contact = $this->ecarrerService->ecarrerSectionsList('life_at_bl_contact');

            if (!empty($ecarrer_contact)) {
                foreach ($ecarrer_contact as $contact_value) {

                    if ($contact_value->category_type == 'contact_us') {
                        $sub_data_contact = [];
                        $sub_data_contact['title_en'] = $contact_value->title_en;
                        $sub_data_contact['title_bn'] = $contact_value->title_bn;
                        $sub_data_contact['slug'] = $contact_value->slug;
                        $sub_data_contact['description_en'] = $contact_value->description_en;
                        $sub_data_contact['description_bn'] = $contact_value->description_bn;

                        $data['contact_us'] = $sub_data_contact;
                    } elseif ($contact_value->category_type == 'connect_us_social') {

                        $sub_data_connect = [];
                        $sub_data_connect['title_en'] = $contact_value->title_en;
                        $sub_data_connect['title_bn'] = $contact_value->title_bn;
                        $sub_data_connect['slug'] = $contact_value->slug;
                        $sub_data_connect['description_en'] = $contact_value->description_en;
                        $sub_data_connect['description_bn'] = $contact_value->description_bn;

                        if (!empty($contact_value->portalItems)) {

                            foreach ($contact_value->portalItems as $social_item) {
                                $connct_social = [];

                                $connct_social['title_en'] = $social_item->title_en;
                                $connct_social['image'] = !empty($social_item->image) ? config('filesystems.image_host_url') . $social_item->image : null;
                                $connct_social['alt_text'] = $social_item->alt_text;
                                $connct_social['alt_links'] = $social_item->alt_links;

                                $sub_data_connect['social_icons'][] = $connct_social;
                            }
                        }

                        $data['connect_us'] = $sub_data_connect;
                    }
                }
            }


            return response()->success($data, 'Data Found!');
        } catch (\Exception $e) {
            return response()->error('Data Not Found!');
        }
    }

    /**
     * eCarrer life at banglalink page
     * @return [type] [description]
     */
    public function lifeAtBanglalink() {

        $data = [];

        //seo data
        $category = "life_at_banglalink";
        $seoData = $this->ecarrerService->getSeoData($category);

        $data['seo_data'] = array(
            'banner_web' => $seoData->image == "" ? "" : config('filesystems.image_host_url') . $seoData->image,
            'banner_mobile' => $seoData->image_mobile == "" ? "" : config('filesystems.image_host_url') . $seoData->image_mobile,
            'alt_text' => $seoData->alt_text,
            'page_header' => $seoData->page_header,
            'schema_markup' => $seoData->schema_markup
        );


        // Life at banglalink 3 general section
        $life_at_bl_general = $this->ecarrerService->ecarrerSectionsList('life_at_bl_general');

//        print_r($life_at_bl_general);die();

        if (!empty($life_at_bl_general) && count($life_at_bl_general) > 0) {
            foreach ($life_at_bl_general as $general_value) {


                if ($general_value->category_type == 'news_on_top') {

                    $data['news_on_top'] = $this->lifeAtBanglalinkData($general_value);
                } elseif ($general_value->category_type == 'values_section') {
                    $data['values_section'] = $this->lifeAtBanglalinkData($general_value);
                } elseif ($general_value->category_type == 'campus_section') {
                    $data['campus_section'] = $this->lifeAtBanglalinkData($general_value);
                }
            }

            if (!isset($data['news_on_top'])) {
                $data['news_on_top'] = null;
            }
            if (!isset($data['values_section'])) {
                $data['values_section'] = null;
            }

            if (!isset($data['campus_section'])) {
                $data['campus_section'] = null;
            }
        } else {
            $data['news_on_top'] = null;
            $data['values_section'] = null;
            $data['campus_section'] = null;
        }





        # Life at banglalink Diversity section
        $life_at_bl_diversity = $this->ecarrerService->ecarrerSectionsList('life_at_bl_diversity');

        if (!empty($life_at_bl_diversity) && count($life_at_bl_diversity) > 0) {
            foreach ($life_at_bl_diversity as $diversity_value) {

                $sub_data = [];
                $sub_data['title_en'] = $diversity_value->title_en;
                $sub_data['title_bn'] = $diversity_value->title_bn;
                $sub_data['slug'] = $diversity_value->slug;
                $sub_data['description_en'] = $diversity_value->description_en;
                $sub_data['description_bn'] = $diversity_value->description_bn;

                if (!empty($diversity_value->portalItems)) {

                    foreach ($diversity_value->portalItems as $portal_items) {
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

                $data['diversity'] = $sub_data;
            } // Foreach end
        } else {
            $data['diversity'] = null;
        }
        // endif
        # Life at banglalink Events and Activites section
        $life_at_bl_events = $this->ecarrerService->ecarrerSectionsList('life_at_bl_events');

        // dd($life_at_bl_events);

        if (!empty($life_at_bl_events) && count($life_at_bl_events) > 0) {

            foreach ($life_at_bl_events as $events_value) {

                $sub_data = [];
                $sub_data['title_en'] = $events_value->title_en;
                $sub_data['title_bn'] = $events_value->title_bn;
                $sub_data['slug'] = $events_value->slug;
                if (!empty($events_value->additional_info)) {
                    $sub_data['sider_info'] = json_decode($events_value->additional_info)->sider_info;
                }

                if (!empty($events_value->portalItems)) {

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
        } else {
            $data['events_activites'] = null;
        }


        # ecarrer Teams section
        #
    	$life_at_bl_teams = $this->ecarrerService->ecarrerSectionsList('life_at_bl_teams');

        if (!empty($life_at_bl_teams) && count($life_at_bl_teams) > 0) {

            $teams = [];
            foreach ($life_at_bl_teams as $teams_value) {

                if ($teams_value->category_type == 'teams_title') {

                    $sub_data = [];
                    $sub_data['title_en'] = $teams_value->title_en;
                    $sub_data['title_bn'] = $teams_value->title_bn;

                    $teams['teams_title'] = $sub_data;
                } else {

                    $sub_data = [];
                    $sub_data['title_en'] = $teams_value->title_en;
                    $sub_data['title_bn'] = $teams_value->title_bn;
                    $sub_data['slug'] = $teams_value->slug;
                    if (!empty($teams_value->additional_info)) {
                        $sub_data['sider_info'] = json_decode($teams_value->additional_info)->sider_info;
                    }

                    if (!empty($teams_value->portalItems) && count($teams_value->portalItems) > 0) {

                        foreach ($teams_value->portalItems as $portal_items) {
                            $sub_items = [];

                            $sub_items['title_en'] = $portal_items->title_en;
                            $sub_items['description_en'] = $portal_items->description_en;
                            $sub_items['description_bn'] = $portal_items->description_bn;
                            $sub_items['image'] = !empty($portal_items->image) ? config('filesystems.image_host_url') . $portal_items->image : null;
                            $sub_items['alt_text'] = $portal_items->alt_text;

                            #teams tab content buttons
                            $sub_items['call_to_action_buttons'] = !empty($portal_items->call_to_action) ? unserialize($portal_items->call_to_action) : null;

                            $sub_data['tab_item_contant'] = $sub_items;
                        }
                    } else {
                        $sub_data['tab_item_contant']['call_to_action_buttons'] = null;
                    }

                    $teams['teams_tab'][] = $sub_data;
                }
            } // Foreach end

            $data['teams'] = $teams;
        } else {
            $data['teams'] = null;
        }



        return response()->success($data, 'Data Found!');
    }

    /**
     * private function for life at banglalink data manupulation
     * @return [type] [description]
     */
    private function lifeAtBanglalinkData($general_value) {

        $sub_data_news = [];
        $sub_data_news['title_en'] = $general_value->title_en;
        $sub_data_news['title_bn'] = $general_value->title_bn;


        if (!empty($general_value->portalItems)) {

            foreach ($general_value->portalItems as $portal_items) {
                $sub_data_news_item = [];

                $sub_data_news_item['title_en'] = $portal_items->title_en;
                $sub_data_news_item['title_bn'] = $portal_items->title_bn;
                $sub_data_news_item['description_en'] = $portal_items->description_en;
                $sub_data_news_item['description_bn'] = $portal_items->description_bn;

                $sub_data_news_item['image'] = !empty($portal_items->image) ? config('filesystems.image_host_url') . $portal_items->image : null;
                $sub_data_news_item['alt_text'] = $portal_items->alt_text;
                $sub_data_news_item['alt_links'] = $portal_items->alt_links;
                $sub_data_news_item['video'] = $portal_items->video;

                $sub_data_news['item_list'][] = $sub_data_news_item;
            }
        }

        return $sub_data_news;
    }

    /**
     * eCarrer vacancy page api
     * @return [type] [description]
     */
    public function getEcarrerVacancy() {

        try {

            $data = [];
            
                   //seo data
        $category = "vacancy";
        $seoData = $this->ecarrerService->getSeoData($category);

        $data['seo_data'] = array(
            'banner_web' => $seoData->image == "" ? "" : config('filesystems.image_host_url') . $seoData->image,
            'banner_mobile' => $seoData->image_mobile == "" ? "" : config('filesystems.image_host_url') . $seoData->image_mobile,
            'alt_text' => $seoData->alt_text,
            'page_header' => $seoData->page_header,
            'schema_markup' => $seoData->schema_markup
        );

            $data['we_hire'] = $this->ecarrerService->getVacancyHire();
            $data['news_media'] = $this->ecarrerService->getVacancyNewsMedia();
            $data['box_icon'] = $this->ecarrerService->getVacancyBoxIcon();
            $data['job_offers'] = $this->ecarrerService->getVacancyLeverJobOffers();
            
            return $data['job_offers'];


            return response()->success($data, 'Data Found!');
        } catch (\Exception $e) {
            return response()->error('Data Not Found!');
        }
    }

    /**
     * [eCarrer Programs category sap, ennovators, aip]
     * @return [type]           [description]
     */
    public function getEcarrerPrograms() {

        try {

            $data = [];
            
               //seo data
        $category = "programs";
        $seoData = $this->ecarrerService->getSeoData($category);

        $data['seo_data'] = array(
            'banner_web' => $seoData->image == "" ? "" : config('filesystems.image_host_url') . $seoData->image,
            'banner_mobile' => $seoData->image_mobile == "" ? "" : config('filesystems.image_host_url') . $seoData->image_mobile,
            'alt_text' => $seoData->alt_text,
            'page_header' => $seoData->page_header,
            'schema_markup' => $seoData->schema_markup
        );
        
            $ecarrer_sap = $this->ecarrerService->getProgramsSap();
            $ecarrer_ennovators = $this->ecarrerService->getProgramsEnnovators();
            $ecarrer_aip = $this->ecarrerService->getProgramsAip();

            if (!empty($ecarrer_sap['tab_title'])) {
                $data['tabs'][]['sap'] = $ecarrer_sap;
            }
            if (!empty($ecarrer_ennovators['tab_title'])) {
                $data['tabs'][]['ennovators'] = $ecarrer_ennovators;
            }
            if (!empty($ecarrer_aip['tab_title'])) {
                $data['tabs'][]['aip'] = $ecarrer_aip;
            }

            return response()->success($data, 'Data Found!');
        } catch (\Exception $e) {
            return response()->error('Data Not Found!');
        }
    }

    /**
     * eCarrer University list api
     * @return [type] [description]
     */
    public function ecarrerUniversity() {

        try {

            $data = [];

            $data['university_list'] = $this->ecarrerService->getUniversityList();


            return response()->success($data, 'Data Found!');
        } catch (\Exception $e) {
            return response()->error('Data Not Found!');
        }
    }

    public function ecarrerApplicationForm(Request $request) {

        try {
            # Image validation check
            $image_upload_size = ConfigController::customerImageUploadSize();
            $image_upload_type = ConfigController::customerImageUploadType();

            $validator = Validator::make($request->all(), [
                        'name' => 'required',
                        'applicant_cv' => 'nullable|mimes:doc,pdf,docx,zip|max:' . $image_upload_size, // 2M
                        'phone' => 'nullable|numeric',
                        'email' => 'nullable|email',
                        'university_id' => 'nullable|integer',
                        'versity_id' => 'nullable|integer',
            ]);
            if ($validator->fails()) {
                // return response()->json($validator->messages()->first(), HttpStatusCode::VALIDATION_ERROR);
                return response()->json((['status' => 'FAIL', 'status_code' => HttpStatusCode::VALIDATION_ERROR, 'message' => $validator->messages()->first(), 'errors' => []]), HttpStatusCode::VALIDATION_ERROR);
            }


            # update application form
            $this->ecarrerService->updateApplicationForm($request->all());

            return response()->success([], 'Form submittd successfuly.');
        } catch (\Exception $e) {
            return response()->json((['status' => 'FAIL', 'status_code' => HttpStatusCode::VALIDATION_ERROR, 'message' => $e->getMessage(), 'errors' => []]), HttpStatusCode::VALIDATION_ERROR);
        }
    }

}

// Class end

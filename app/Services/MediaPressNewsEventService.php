<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:23
 */

namespace App\Services;

use App\Repositories\AdTechRepository;
use App\Repositories\ComponentRepository;
use App\Repositories\MediaBannerImageRepository;
use App\Repositories\MediaNewsCategoryRepository;
use App\Repositories\MediaPressNewsEventRepository;
use App\Repositories\MediaLandingPageRepository;
use App\Services\Assetlite\ComponentService;

class MediaPressNewsEventService extends ApiBaseService
{
    /**
     * @var $sliderRepository
     */
    protected $mediaPNERepository;
    /**
     * @var MediaBannerImageRepository
     */
    private $mediaBannerImageRepository;
    /**
     * @var ComponentRepository
     */
    private $componentRepository;
    /**
     * @var AdTechRepository
     */
    private $adTechRepository;
    /**
     * @var MediaNewsCategoryRepository
     */
    private $mediaNewsCategoryRepository;

    protected $mediaLandingPageRepository;
    /**
     * DigitalServicesService constructor.
     * @param MediaPressNewsEventRepository $mediaPNERepository
     * @param MediaBannerImageRepository $mediaBannerImageRepository
     */
    public function __construct(
        MediaPressNewsEventRepository $mediaPNERepository,
        MediaBannerImageRepository $mediaBannerImageRepository,
        ComponentRepository $componentRepository,
        AdTechRepository $adTechRepository,
        MediaNewsCategoryRepository $mediaNewsCategoryRepository,
        MediaLandingPageRepository $mediaLandingPageRepository
    ) {
        $this->mediaLandingPageRepository = $mediaLandingPageRepository;
        $this->mediaPNERepository = $mediaPNERepository;
        $this->mediaBannerImageRepository = $mediaBannerImageRepository;
        $this->componentRepository = $componentRepository;
        $this->adTechRepository = $adTechRepository;
        $this->mediaNewsCategoryRepository = $mediaNewsCategoryRepository;
    }

    public function mediaPressEventData($moduleType)
    {
        $pressRelease = $this->mediaPNERepository->getPressNewsEvent($moduleType);
        $bannerImage = $this->mediaBannerImageRepository->bannerImage($moduleType);
        $message = ucfirst(str_replace('_', ' ', $moduleType));
        $data = [
            "body_section" => $pressRelease,
            'banner_image' => $bannerImage
        ];
        return $this->sendSuccessResponse($data, "$message Data");
    }

    public function mediaPressEventFilterData($moduleType, $from, $to)
    {
        $data = $this->mediaPNERepository->filterByDate($moduleType, $from, $to);
        $message = ucfirst(str_replace('_', ' ', $moduleType));
        return $this->sendSuccessResponse($data, "$message Filter Data");
    }

    public function detailsComponent($urlSlug, $referenceType = false)
    {
        $post = $this->mediaPNERepository->getDataBySlug($urlSlug);
        $blogDetails = [];
        if (!empty($post->id)) {
            $blogDetails['components'] =  $this->componentRepository->getComponentByPageType($referenceType, $post->id);
            $blogDetails['post'] = $post;

            if ($referenceType != "csr") {
                $blogDetails['related_blogs'] = $this->mediaPNERepository->getRelatedBlog($post->id,$post->media_news_category_id);
                $blogDetails['ad_tech'] = $this->adTechRepository->findOneByProperties(['reference_id' => $post->id,'reference_type' => "blog"]);
            }
        }

        return $this->sendSuccessResponse($blogDetails, "Blog details component");
    }

    public function filterArchive($type, $param, $limit)
    {
        $banner = $this->mediaLandingPageRepository->findOneByProperties(['component_type'=> 'news_archive'],['title_en','title_bn', 'short_desc_en', 'short_desc_bn']);
        $data = $this->mediaPNERepository->filterArchive($type, $param, $limit);
        $custom = collect(['banner'=>$banner]);
        $res = $custom->merge($data);
        return $this->sendSuccessResponse($res, "Filter Date");
    }

    public function topicList()
    {
        $data = $this->mediaNewsCategoryRepository->findByProperties([]);
        return $this->sendSuccessResponse($data, "Topic List");
    }
}

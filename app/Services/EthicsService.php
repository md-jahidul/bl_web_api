<?php

namespace App\Services;

use App\Repositories\EthicsRepository;
use Exception;

/**
 * Class EthicsService
 * @package App\Services
 */
class EthicsService extends ApiBaseService
{

    /**
     * @var EthicsRepository
     */
    protected $ethicsRepository;

    /**
     * @var ImageFileViewerService
     */
    protected $imageFileViewerService;

    /**
     * EthicsService constructor.
     * @param EthicsRepository $ethicsRepository
     * @param ImageFileViewerService $imageFileViewerService
     */
    public function __construct(
        EthicsRepository $ethicsRepository,
        ImageFileViewerService $imageFileViewerService
    ) {
        $this->ethicsRepository = $ethicsRepository;
        $this->imageFileViewerService = $imageFileViewerService;
    }

    /**
     * Request for ethics data
     *
     * @return mixed|string
     */
    public function getData()
    {
        try {
            $data = $this->ethicsRepository->getPageInfo();

            $keyData = config('filesystems.moduleType.EthicsAndComplianceBanner');
            if ($data) {
                $imgData = $this->imageFileViewerService->prepareImageData($data, $keyData);
                $data = array_merge($data->toArray(), $imgData);
            }

            $files = $this->ethicsRepository->getFiles();

            $fileKeyData = config('filesystems.moduleType.EthicsFiles');
            foreach ($files as $key => $file) {
                $fileData = $this->imageFileViewerService->prepareImageData($file, $fileKeyData);
                $files[$key] = array_merge($file->toArray(), $fileData);
            }


            $data['files'] = $files;


            return $this->sendSuccessResponse($data, 'Ethics data');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }
}

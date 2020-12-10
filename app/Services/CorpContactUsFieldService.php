<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:23
 */

namespace App\Services;

use App\Repositories\CorpContactUsFieldRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class CorpContactUsFieldService
{
    use CrudTrait;
    use FileTrait;

    /**
     * @var CorpContactUsFieldRepository
     */
    private $contactUsFieldRepository;

    /**
     * DigitalServicesService constructor.
     * @param CorpContactUsFieldRepository $contactUsFieldRepository
     */
    public function __construct(CorpContactUsFieldRepository $contactUsFieldRepository)
    {
        $this->contactUsFieldRepository = $contactUsFieldRepository;
        $this->setActionRepository($contactUsFieldRepository);
    }

    public function getContactPageWiseField($pageId)
    {
        return $this->contactUsFieldRepository->findByProperties(['page_id' => $pageId]);
    }

    /**
     * Storing the alFaq resource
     * @param $data
     * @param $pageType
     * @param $sectionId
     * @return Response
     */
    public function storeField($data, $sectionId)
    {
        $data['field_name'] = str_replace(' ', '_', strtolower($data['input_label_en']));
        $data['page_id'] = $sectionId;
        $this->save($data);
        return new Response("Field has been successfully created");
    }

    /**
     * Updating the banner
     * @param $data
     * @return Response
     */
    public function updateField($data, $sectionId, $id)
    {
        $field = $this->findOne($id);
        $field->update($data);
        return Response('Field has been successfully updated');
    }

    /**
     * @param $id
     * @return ResponseFactory|Response
     * @throws \Exception
     */
    public function deleteField($id)
    {
        $component = $this->findOne($id);
        $component->delete();
        return Response('Field has been successfully deleted');
    }
}

<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:34
 */

namespace App\Traits;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait CrudTrait
 * @package App\Traits
 */
trait CrudTrait
{
    /**
     * @var $actionRepository
     */
    private $actionRepository;


    /**
     * @param BaseRepository $actionRepository
     */
    public function setActionRepository(BaseRepository $actionRepository): void
    {
        $this->actionRepository = $actionRepository;
    }


    /**
     * @param $id
     * @param null $relation
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findOne($id, $relation = null)
    {
        return $this->actionRepository->findOne($id, $relation);
    }


    /**
     * @param null $perPage
     * @param null $relation
     * @param array|null $orderBy
     * @return \App\Repositories\Contracts\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]
     */
    public function findAll($perPage = null, $relation = null, array $orderBy = null)
    {
        return $this->actionRepository->findAll($perPage, $relation, $orderBy);
    }

    /**
     * @param array $searchCriteria
     * @param null $relation
     * @param array|null $orderBy
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findBy(array $searchCriteria = [], $relation = null, array $orderBy = null)
    {
        return $this->actionRepository->findBy($searchCriteria, $relation, $orderBy);
    }

    /**
     * @param Model $model
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model|mixed
     */
    public function update(Model $model, array $data)
    {
        return $this->actionRepository->update($model, $data);
    }


    /**
     * @param $id
     * @return bool|mixed|null
     * @throws \Exception
     */
    public function delete($id)
    {
        $model = $this->actionRepository->findOrFail($id);
        return $model->delete();
    }


    /**
     * @param $id
     * @param null $relation
     * @param array|null $orderBy
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Model[]|mixed
     */
    public function findOrFail($id, $relation = null, array $orderBy = null)
    {
        return $this->actionRepository->findOrFail($id);
    }


    /**
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function save(array $data)
    {
        return $this->actionRepository->save($data);
    }


    /**
     * @param $request
     * @param $imageTitle
     * @param $location
     * @return string
     */
    public function imageUpload($request, $imgColumnName, $imageTitle, $location)
    {
        $file_name = str_replace(' ', '-', strtolower($imageTitle));
        $upload_date = date('Y-m-d-h-i-s');
        $image = request()->file($imgColumnName);
        $fileType = $image->getClientOriginalExtension();
        $imageName = $upload_date . '_' . $file_name . '.' . $fileType;
        $directory = $location;
        $imageUrl = $imageName;
        $image->move(public_path($directory), $imageName);
        return $imageUrl;
    }

}

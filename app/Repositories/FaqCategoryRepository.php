<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\AlFaqCategory;

class FaqCategoryRepository extends BaseRepository
{
    public $modelName = AlFaqCategory::class;

    public function getData($slug, $id)
    {
        return $this->model->where('slug', $slug)
            ->select('slug', 'name_en', 'name_bn', 'description_en', 'description_bn')
            ->with(['faqs' => function($q) use($id){
                $q->where('status', 1);
                $q->where('model_id', $id);
                $q->orderBy('display_order', 'ASC');
                $q->select(
                    'id', 'slug',
                    'question_en', 'question_bn',
                    'answer_en', 'answer_bn', 'display_order'
                );
                }])
            ->first();
    }
}

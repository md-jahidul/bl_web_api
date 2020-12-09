<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorpCaseStudyReportSection extends Model
{
    public function components()
    {
        return $this->hasMany(CorpCaseStudyReportComponent::class, 'section_id', 'id');
    }
}

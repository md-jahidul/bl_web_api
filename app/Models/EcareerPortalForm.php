<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EcareerPortalForm extends Model
{

	protected $table = 'ecareer_portal_forms';

   protected $fillable = [
       'name',
       'phone',
       'email',
       'university_id',
       'versity_id',
       'gender',
       'date_of_birth',
       'cgpa',
       'address',
       'applicant_cv',
       'additional_info',
   ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EcarrerPortalForm extends Model
{

	protected $table = 'ecarrer_portal_forms';
	
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

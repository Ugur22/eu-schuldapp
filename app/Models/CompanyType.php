<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyType extends Model
{
   protected $table = 'company_types';

   public function companies()
   {
       return $this->belongsToMany('App\Models\Company', 'companies_types', 'type_id', 'company_id');
   }
}

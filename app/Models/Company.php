<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
   protected $table = 'companies';

   public function types()
   {
       return $this->belongsToMany('App\Models\Company', 'companies_types', 'type_id', 'company_id');
   }
}

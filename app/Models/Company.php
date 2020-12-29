<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
   protected $table = 'companies';

   public function location()
   {
       return $this->belongsTo('App\Models\Place', 'place_id');
   }

   public function types()
   {
       return $this->belongsToMany('App\Models\CompanyType', 'companies_types', 'company_id', 'type_id');
   }

   public function debtor()
   {
       return $this->belongsToMany('App\Models\CompanyType', 'companies_types', 'company_id', 'type_id')->whereIn('type_id', [2,3,4]);
   }
}

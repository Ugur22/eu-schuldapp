<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientIncome extends Model
{
   protected $table = 'client_incomes';

   public function income()
   {
       return $this->belongsTo('App\Models\Income', 'income_id');
   }

   public function client()
   {
       return $this->belongsTo('App\Models\Client', 'client_id');
   }

   public function employer()
   {
       return $this->belongsTo('App\Models\Company', 'employer_id');
   }
}

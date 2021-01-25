<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientOutcome extends Model
{
   protected $table = 'client_outcomes';

   public function outcome()
   {
       return $this->belongsTo('App\Models\Outcome', 'outcome_id');
   }

   public function client()
   {
       return $this->belongsTo('App\Models\Client', 'client_id');
   }

   public function company()
   {
       return $this->belongsTo('App\Models\Company', 'company_id');
   }
}

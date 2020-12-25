<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
   protected $table = 'templates';

   public function clientStep()
   {
      return $this->belongsTo('App\Models\ClientStatus', 'client_status_id');
   }

   public function debtStep()
   {
      return $this->belongsTo('App\Models\ClientDebtStatus', 'client_debt_status_id');
   }
}

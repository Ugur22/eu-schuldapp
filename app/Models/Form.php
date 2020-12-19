<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
   protected $table = 'template';

   public function clientStatus()
   {
       return $this->belongsTo('App\Models\ClientStatus', 'Client_Schuld_Status', 'Status');
   }

   public function clientDebtStatus()
   {
       return $this->belongsTo('App\Models\ClientDebtStatus', 'Client_Schuld_Status', 'Status');
   }
}

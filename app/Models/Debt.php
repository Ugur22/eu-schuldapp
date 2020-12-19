<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
   protected $table = 'client_schuld';

   public function client()
   {
       return $this->hasMany('App\Models\User', 'BSN', 'Client');
   }

   public function status()
   {
       return $this->belongsTo('App\Models\ClientDebtStatus', 'Status', 'Status');
   }

   public function debtor()
   {
       return $this->belongsTo('App\Models\Company', 'Incasseerder', 'Company');
   }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
   protected $table = 'document';

   public function client()
   {
       return $this->belongsTo('App\Models\Client', 'Client', 'BSN');
   }

   public function clientDebt()
   {
       return $this->belongsTo('App\Models\Debt', 'Client_Schuld_ID', 'ID');
   }
}

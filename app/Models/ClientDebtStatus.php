<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
   protected $table = 'client_schuld_status';

   public function previous()
   {
       return $this->belongsTo('App\Models\Debt', 'Previous', 'Status');
   }
}

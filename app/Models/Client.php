<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
   protected $table = 'client';

   public function status()
   {
      return $this->belongsTo('App\Models\ClientStatus', 'Status', 'Status');
   }

   public function consultant()
   {
      return $this->belongsTo('App\Models\Consultant', 'Consultant', 'Consultant');
   }

   public function employer()
   {
      return $this->belongsTo('App\Models\Company', 'Employer', 'Company');
   }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
   protected $table = 'appointments';

   public function client()
   {
       return $this->belongsTo('App\Models\Client', 'client_id');
   }

   public function consultant()
   {
       return $this->belongsTo('App\Models\Consultant', 'consultant_id');
   }

   public function location()
   {
       return $this->belongsTo('App\Models\Place', 'location_id');
   }
}

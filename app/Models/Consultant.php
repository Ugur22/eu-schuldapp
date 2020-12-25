<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultant extends Model
{
   protected $table = 'consultants';

   public function user()
   {
       return $this->belongsTo('App\Models\User', 'user_id');
   }

   public function clients()
   {
      return $this->hasMany('App\Models\Client', 'consultant_id');
   }

   public function appointments()
   {
      return $this->hasMany('App\Models\Appointment', 'consultant_id')->with('location');
   }

   public function location()
   {
       return $this->belongsTo('App\Models\Place', 'place_id');
   }
}

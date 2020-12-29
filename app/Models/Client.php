<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
   protected $table = 'clients';

   public function user()
   {
       return $this->belongsTo('App\Models\User', 'user_id');
   }

   public function debts()
   {
       return $this->hasMany('App\Models\Debt', 'client_id');
   }

   public function status()
   {
      return $this->belongsTo('App\Models\ClientStatus', 'client_status_id');
   }

   public function consultant()
   {
      return $this->belongsTo('App\Models\Consultant', 'consultant_id')->with('user');
   }

   public function appointments()
   {
      return $this->hasMany('App\Models\Appointment', 'client_id')->with('location');
   }

   public function location()
   {
       return $this->belongsTo('App\Models\Place', 'place_id');
   }

   public function employer()
   {
       return $this->belongsTo('App\Models\Company', 'employer_id');
   }

   public function children()
   {
       return $this->hasMany('App\Models\Child', 'client_id');
   }
}

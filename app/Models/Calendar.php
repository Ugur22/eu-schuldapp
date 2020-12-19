<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
   protected $table = 'calendar';

   public function client()
   {
       return $this->hasMany('App\Models\User', 'BSN', 'Client');
   }
}

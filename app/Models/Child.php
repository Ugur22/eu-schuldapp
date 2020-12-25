<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
   protected $table = 'client_children';

   public function client()
   {
       return $this->belongsTo('App\Models\Client', 'client_id');
   }
}

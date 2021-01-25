<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outcome extends Model
{
   protected $table = 'outcomes';

   public function client()
   {
       return $this->hasMany('App\Models\ClientOutcome', 'outcome_id');
   }
}

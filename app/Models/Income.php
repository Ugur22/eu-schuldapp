<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
   protected $table = 'incomes';

   public function client()
   {
       return $this->hasMany('App\Models\ClientIncome', 'income_id');
   }
}

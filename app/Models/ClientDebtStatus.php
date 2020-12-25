<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientDebtStatus extends Model
{
   protected $table = 'client_debt_statuses';

   public function previous()
   {
       return $this->belongsTo('App\Models\Debt', 'previous');
   }
}

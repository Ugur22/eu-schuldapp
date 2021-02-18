<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
   protected $table = 'client_debts';

   public function client()
   {
       return $this->belongsTo('App\Models\Client', 'client_id');
   }

   public function status()
   {
       return $this->belongsTo('App\Models\ClientDebtStatus', 'status_id')->with('next');
   }

   public function debtor()
   {
       return $this->belongsTo('App\Models\Company', 'debtor_id');
   }

   public function outboxes()
   {
       return $this->belongsTo('App\Models\Outbox', 'client_debt_id');
   }

   public function documents()
   {
       return $this->hasMany('App\Models\Document', 'client_debt_id')->with('file')->with('html');
   }
}

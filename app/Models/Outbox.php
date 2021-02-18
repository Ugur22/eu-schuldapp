<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outbox extends Model
{
   protected $table = 'outbox';

   public function client()
   {
      return $this->belongsTo('App\Models\Client');
   }

   public function status()
   {
      return $this->belongsTo('App\Models\OutboxStatus');
   }

   public function debt()
   {
      return $this->belongsTo('App\Models\Debt', 'client_debt_id')->with('debtor');
   }

   public function documents()
   {
      return $this->belongsToMany('App\Models\Document', 'outbox_documents', 'outbox_id', 'document_id')->with('file');
   }
}

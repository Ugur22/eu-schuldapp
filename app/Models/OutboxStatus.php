<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutboxStatus extends Model
{
   protected $table = 'outbox_statusses';

   public function outboxes()
   {
      return $this->hasMany('App\Models\Outbox');
   }
}

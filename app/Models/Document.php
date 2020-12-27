<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
   protected $table = 'documents';

   public function client()
   {
       return $this->belongsTo('App\Models\Client');
   }

   public function clientDebt()
   {
       return $this->belongsTo('App\Models\Debt')->with('debtor');
   }

   public function clientStatus()
   {
       return $this->belongsTo('App\Models\ClientStatus');
   }

   public function template()
   {
       return $this->belongsTo('App\Models\Template');
   }

   public function file()
   {
       return $this->hasOne('App\Models\DocFile', 'doc_id');
   }

   public function html()
   {
       return $this->hasOne('App\Models\DocHtml', 'doc_id');
   }
}

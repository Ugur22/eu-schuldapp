<?php
namespace App\Helpers;
use App\Models\Outbox;
use App\Models\Template;
use App\Models\Document;

class OutboxHelpers
{
  public function createOutboxClient($client_id, $client_status, $debt_status=null)
  {
    $templates = Template::where('client_status_id', $client_status)->where('client_debt_status_id', $debt_status)->whereMain(1)->orderBy('id')->get();
    foreach ($templates as $key => $tpl) {
      $doc = Document::where('client_id', $client_id)->where('client_status_id', $client_status)->where('template_id', $tpl->id)->first();
      if (is_null($debt_status)) {
        $docs = $this->clientStatusOutbox($client_status);
      } else {
        $docs = $this->debtStatusOutbox($debt_status);
      }
      $outbox = new Outbox;
      $outbox->client_id = $client_id;
      $outbox->to = $tpl->sendto;
      $outbox->client_debt_id = $doc->client_debt_id;
      $subject = array_keys($docs);
      $outbox->subject = $subject[$key];
      $outbox->status_id = 2;
      if ($outbox->save()) {
        $array = $docs[$subject[$key]];
        $tpl_ids = [];
        $files = [];
        $i = 0;
        $f = 0;
        foreach ($array as $key => $value) {
          if (is_int($value)) {
            $tpl_ids[$i] = $value;
            $i++;
          } else {
            $files[$f] = $value;
            $f++;
          }
        }
        $docTpls = [];
        $docFiles = [];
        if (count($tpl_ids)) {
          $docTpls = Document::whereIn('template_id', $tpl_ids)->pluck('id')->toArray();
        }
        if (count($files)) {
          $docFiles = Document::whereHas('file', function($q) {
            $q->whereIn('fileoption', $files);
          })->pluck('id')->toArray();
        }
        if (count($docTpls) && count($docFiles)) {
          $documents = array_merge($docTpls, $docFiles);
        } elseif (count($docTpls) && !count($docFiles)) {
          $documents = $docTpls;
        }
        $outbox->documents()->attach($documents);
      }
    }
  }

  public function fileToOutbox($document_id, $client_id, $option, $debt_id=null)
  {
    $outbox = Outbox::where('client_id', $client_id)->where('client_debt_id', $debt_id);
    if (in_array($option, ['id-client', 'id-client-back', 'id-partner', 'id-partner-back'])) {
      $outbox = $outbox->where('subject', 'Rekeningaanvragen')->first();
    } elseif ($option == 'vtlb') {
      $outbox = $outbox->where('subject', '1stVoorstel')->first();
    } elseif ($option == 'proposal-confirmation') {
      $outbox = $outbox->where('subject', 'Volledig traject afgerond')->first();
    }
    $outbox->documents()->attach($document_id);
  }

  public function clientStatusOutbox($status)
  {
    switch ($status) {
      case 3:
        $documents = [
          'Contracten met Client' => [2, 3, 4, 5, 6, 7, 8, 9]
        ];
        break;
      case 4:
        $documents = [
          'Welkomsbrief' => [10],
          'Aankondiging werkgever' => [11],
          'Rekeningaanvragen' => [12, 'id-client', 'id-client-back', 'id-partner', 'id-partner-back'],
          'Verzoek klant' => [13],
        ];
        break;
      
      default:
        $documents = [];
        break;
    }

    return $documents;
  }

  public function debtStatusOutbox($status)
  {
    switch ($status) {
      case 2:
        $documents = [
          'Aanmelding Schuldhulpverlening' => [14, 1, 3, 8]
        ];
        break;
      case 4:
        $documents = [
          '1stVoorstel' => [18, 'vtlb', 15, 16, 17]
        ];
        break;
      case 5:
        $documents = [
          'Volledig traject afgerond' => [18, 'proposal-confirmation']
        ];
        break;
      
      default:
        $documents = [];
        break;
    }

    return $documents;
  }
}
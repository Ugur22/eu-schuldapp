<?php
namespace App\Helpers;
use Illuminate\Support\Facades\File;
use App\Models\DocHtml;
use App\Helpers\ControllerHelpers;

class TemplateHelpers
{
  public function templateDateFormat($html, $object, $format='d-m-Y', $modified=[])
  {
    preg_match_all("/\<\!\-\-(date_format)\(([a-z_]+)\)\-\-\>/", $html, $outputs);
    $string = $html;
    foreach($outputs[2] as $k => $output){
      foreach ($modified as $key => $value) {
        $object[$key] = \Carbon\Carbon::parse($value)->format($format);
      }
      if($object[$output]){
        $datetime = $object[$output] ? \Carbon\Carbon::parse($object[$output])->format($format): $object[$output];
        $replaced = str_replace($outputs[0][$k], $datetime, $string);
        $string = $replaced;
      }
    }
    return $string;
  }

  public function signedDate($html, $party='client'){
    $signed_date = \Carbon\Carbon::now()->format('D, j M Y');
    $html = str_replace('<!--date_signed_by_'. $party .'-->', $signed_date, $html);
    return $html;
  }

  public function countRows($html, $rows)
  {
    preg_match_all("/\<\!\-\-(count)\(([a-z_]+)\)\-\-\>/", $html, $outputs);
    $string = $html;
    foreach($outputs[2] as $k => $output){
      $count = is_array($rows) ? count($rows): $rows->count();
      $replaced = str_replace($outputs[0][$k], $count, $string);
      $string = $replaced;
    }
    return $string;
  }

  public function templateStr($html, $object, $modified=[])
  {
    preg_match_all("/\<\!\-\-([a-z_]+)\-\-\>/", $html, $outputs);
    $string = $html;
    foreach($outputs[1] as $k => $output){
        foreach ($modified as $key => $value) {
          $object[$key] = $value;
        }
        $str = $object[$output];
        if ($str) {
          switch ($str) {
            case 'm':
              $str = 'Dhr.';
              break;
            case 'f':
              $str = 'Mevr.';
              break;
            default:
              $str = $str;
              break;
          }
          $replaced = str_replace($outputs[0][$k], $str, $string);
          $string = $replaced;
        }
    }
    return $string;
  }

  public function switchDisplay($html, $ifs){
    preg_match_all("/(?:\<\!\-\-(if)\(([a-z_]+)\)\-\-\>)((.|\n)*?)(?:\<\!\-\-(endif)\(([a-z_]+)\)\-\-\>)/", $html, $if_output);

    foreach ($if_output[2] as $key => $value) {
        if(!$ifs[$value] || $ifs[$value] && is_object($ifs[$value]) && !$ifs[$value]->count()){
          $str = str_replace($if_output[3][$key], '', $html);
          $html = $str;
        }
    }

    return $html;
  }

  public function looping($html, $data)
  {
    preg_match_all("/(?:\<\!\-\-(loop)\(([a-z_]+)\)\-\-\>)((.|\n)*?)(?:\<\!\-\-(endloop)\(([a-z_]+)\)\-\-\>)/", $html, $looping);

    $loophtml = '';
    foreach ($looping[2] as $key => $value) {
      foreach ($data[$value] as $k => $val) {
        $htmloop = $this->templateDateFormat($looping[3][$key], $val);
        $loophtml .= $this->templateStr($htmloop, $val);
      }
      $html = str_replace($looping[3][$key], $loophtml, $html);
    }

    return $html;
  }

  public function signatureRequired ($html) {
    preg_match_all("/\<\!\-\-(sign_)([a-z_]+)\-\-\>/", $html, $outputs);
    if (!$outputs[2]) {
      return false;
    }else{
      return array_values(array_unique($outputs[2]));
    }
  }

  public function findSignaturePlaceholder($doc, $party, $ext)
  {
    $html = $doc->html->html;
    preg_match_all("/\<\!\-\-(sign_)([a-z_]+)\-\-\>/", $html, $outputs);
    
    $string = $html;
    foreach($outputs[2] as $k => $output){
      if ($output == $party) {
        $replaced = str_replace($outputs[0][$k], url('files/signatures/' . str_pad($doc->client->id, 4, '0', STR_PAD_LEFT) . '/' . str_pad($doc->id, 8, '0', STR_PAD_LEFT) .'/'. $output . '.'. $ext), $string);
        $string = $replaced;
      }
    }
    return $string;
  }

  public function uploadSignature($file, $doc, $signer)
  {
    $folderPath = public_path('files/signatures/' . str_pad($doc->client->id, 4, '0', STR_PAD_LEFT) . '/' . str_pad($doc->id, 8, '0', STR_PAD_LEFT));
    if(!File::exists($folderPath)){
      File::makeDirectory($folderPath, 0775, true);
    }
    $image_parts = explode(";base64,", $file);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
    $image_base64 = base64_decode($image_parts[1]);
    $file = $folderPath . '/' . $signer . '.'.$image_type;
    if(file_put_contents($file, $image_base64)){
      $html = $this->findSignaturePlaceholder($doc, $signer, $image_type);
      $html = $this->signedDate($html, $signer);
      $dochtml = DocHtml::where('doc_id', $doc->id)->first();
      $dochtml->html = $html;
      $needtobesigned = $this->signatureRequired($html);
      $dochtml->signed = !$needtobesigned ? 1: null;
      $dochtml->save();

      return $dochtml;
    }else{
      return false;
    }
  }

  public function templateToDoc($slug, $user, $doc, $html)
  {
    $controller = new ControllerHelpers;
    switch ($slug) {
      case '1-0-inschrijf-form':
        $ifloop = [
          'children' => $user->children,
          'initial' => $user->partner_lastname
        ];
        $html = $this->countRows($html, $user->children);
        $html = $this->switchDisplay($html, $ifloop);
        $html = $this->looping($html, $ifloop);
        $modifieds['place_name'] = $user->location->name;
        $html = $this->templateDateFormat($html, $user);
        $html = $this->templateStr($html, $user, $modifieds);
        break;
      case 'contract-client':
        $html = $this->templateDateFormat($html, $doc);
        $html = $this->templateStr($html, $user);
        break;
      case '1-1-1-2-akte-van-cessie':
        $modifieds = [
          'place_name' => $user->location->name,
          'employer_name' => $user->employer->name,
          'employer_address' => $user->employer->address,
          'employer_postal_code' => $user->employer->postal_code,
          'employer_place_name' => $user->employer->location->name,
          'employer_phone' => $user->employer->phone,
        ];
        $html = $this->templateDateFormat($html, $user);
        $html = $this->templateStr($html, $user, $modifieds);
        break;
      case '1-3-machtigingsysteem':
        $ifloop = [
          'children' => $user->children,
          'initial' => $user->partner_lastname
        ];
        $html = $this->switchDisplay($html, $ifloop);
        $html = $this->looping($html, $ifloop);
        $modifieds['place_name'] = $user->location->name;
        $html = $this->templateDateFormat($html, $user);
        $html = $this->templateStr($html, $user, $modifieds);
        break;
      case '1-4-machtigingsformulier-auto-incasso':
        $ifloop = [
          'children' => $user->children,
          'initial' => $user->partner_lastname
        ];
        $html = $this->switchDisplay($html, $ifloop);
        $html = $this->looping($html, $ifloop);
        $modifieds['place_name'] = $user->location->name;
        $html = $this->templateDateFormat($html, $user);
        $html = $this->templateStr($html, $user, $modifieds);
        break;
      case '1-5-schuldhulp-contract':
        $modifieds['place_name'] = $user->location->name;
        $html = $this->templateDateFormat($html, $user);
        $html = $this->templateStr($html, $user, $modifieds);
        break;
      case '1-6-stabilisatie-overeenkomst':
        $modifieds['place_name'] = $user->location->name;
        $html = $this->templateDateFormat($html, $user);
        $html = $this->templateStr($html, $user, $modifieds);
        break;
      case '1-7-akte-van-verpanding':
        $modifieds['place_name'] = $user->location->name;
        $html = $this->templateDateFormat($html, $user);
        $html = $this->templateStr($html, $user, $modifieds);
        break;
      case '1-8-volmacht-verstrekt-door-client-eu':
        $modifieds['place_name'] = $user->location->name;
        $html = $this->templateDateFormat($html, $doc);
        $html = $this->templateDateFormat($html, $user);
        $html = $this->templateStr($html, $user, $modifieds);
        break;
      case '1-9-welkomsbrief':
        $modifieds = [
          'consultant_firstname' => $user->consultant->firstname,
          'consultant_lastname' => $user->consultant->lastname,
        ];
        $html = $this->templateDateFormat($html, $doc);
        $html = $this->templateStr($html, $user, $modifieds);
        break;
      case '1-10-aankondiging-werkgever':
        $modifieds = [
          'consultant_firstname' => $user->consultant->firstname,
          'consultant_lastname' => $user->consultant->lastname,
        ];
        $html = $this->templateDateFormat($html, $doc);
        $html = $this->templateStr($html, $user, $modifieds);
        break;
      case '1-11-rekeningaanvragen':
        $html = $this->templateDateFormat($html, $doc);
        $html = $this->templateStr($html, $user);
        break;
      case '1-12-verzoek-klant':
        $html = $this->templateDateFormat($html, $doc);
        $html = $this->templateStr($html, $user);
        break;
      case '2-1-stap4':
        $html = $this->templateDateFormat($html, $doc);
        $modifieds = [
          'debt_collector' => $doc->clientDebt->debtor->name,
          'sent_at' => \Carbon\Carbon::parse($doc->clientDebt->created_at)->format('d-m-Y'),
          'reference_id' => $doc->clientDebt->reference_id
        ];
        $html = $this->templateStr($html, $doc, $modifieds);
        $html = $this->templateStr($html, $user);
        break;
      case '2-0-schulden':
        $ifloop = [
          'children' => $user->children,
          'initial' => $user->partner_lastname
        ];
        $html = $this->switchDisplay($html, $ifloop);
        $html = $this->countRows($html, $user->children);
        $html = $this->looping($html, $ifloop);
        $modifieds['place_name'] = $user->location->name;
        $html = $this->templateDateFormat($html, $user);
        $html = $this->templateStr($html, $user, $modifieds);
        $html = str_replace('<!--client_debts-->', $controller->debtTable($user->id), $html);
        break;
      case '2-0-inkomsten':
        $ifloop = [
          'children' => $user->children,
          'initial' => $user->partner_lastname
        ];
        $html = $this->switchDisplay($html, $ifloop);
        $html = $this->countRows($html, $user->children);
        $html = $this->looping($html, $ifloop);
        $modifieds['place_name'] = $user->location->name;
        $html = $this->templateDateFormat($html, $user);
        $html = $this->templateStr($html, $user, $modifieds);
        $html = str_replace('<!--client_incomes-->', $controller->incomesTable($user->id), $html);
        break;
      case '2-0-uitgaven':
        $ifloop = [
          'children' => $user->children,
          'initial' => $user->partner_lastname
        ];
        $html = $this->switchDisplay($html, $ifloop);
        $html = $this->countRows($html, $user->children);
        $html = $this->looping($html, $ifloop);
        $modifieds['place_name'] = $user->location->name;
        $html = $this->templateDateFormat($html, $user);
        $html = $this->templateStr($html, $user, $modifieds);
        $html = str_replace('<!--client_outcomes-->', $controller->outcomesTable($user->id), $html);
        break;
      case '3-0-1stvoorstel':
        $html = $this->templateDateFormat($html, $doc);
        $modifieds = [
          'debt_collector' => $doc->clientDebt->debtor->name,
          'reference_id' => $doc->clientDebt->reference_id,
          'terms' => $doc->clientDebt->terms,
          'redeem_per_month' => $doc->clientDebt->redeem_per_month,
        ];
        $html = $this->templateStr($html, $doc, $modifieds);
        $html = $this->templateStr($html, $user);
        break;
      case '3-1-2devoorstel':
        $html = $this->templateDateFormat($html, $doc);
        $modifieds = [
          'debt_collector' => $doc->clientDebt->debtor->name,
          'reference_id' => $doc->clientDebt->reference_id,
          'terms' => $doc->clientDebt->terms,
          'redeem_per_month' => $doc->clientDebt->redeem_per_month,
        ];
        $html = $this->templateStr($html, $doc, $modifieds);
        $html = $this->templateStr($html, $user);
        break;
      case '3-2-aankondiging-eenzijdige-overeenkomst':
        $html = $this->templateDateFormat($html, $doc);
        $modifieds = [
          'debt_collector' => $doc->clientDebt->debtor->name,
          'sent_at' => \Carbon\Carbon::parse($doc->clientDebt->created_at)->format('d-m-Y'),
          'reference_id' => $doc->clientDebt->reference_id
        ];
        $html = $this->templateStr($html, $doc, $modifieds);
        $html = $this->templateStr($html, $user);
        break;
      case '3-3-aankondiging-dwangakkoord':
        $html = $this->templateDateFormat($html, $doc);
        $modifieds = [
          'debt_collector' => $doc->clientDebt->debtor->name,
          'sent_at' => \Carbon\Carbon::parse($doc->clientDebt->created_at)->format('d-m-Y'),
          'reference_id' => $doc->clientDebt->reference_id
        ];
        $html = $this->templateStr($html, $doc, $modifieds);
        $html = $this->templateStr($html, $user);
        break;
      case '3-4-dwangakkoord':
        $html = $this->templateDateFormat($html, $doc);
        $modifieds = [
          'debt_collector' => $doc->clientDebt->debtor->name,
          'reference_id' => $doc->clientDebt->reference_id
        ];
        $html = $this->templateStr($html, $doc, $modifieds);
        $html = $this->templateStr($html, $user);
        break;
      case '3-5-eenzijdige-overeenkomst':
        $html = $this->templateDateFormat($html, $doc);
        $modifieds = [
          'debt_collector' => $doc->clientDebt->debtor->name,
          'reference_id' => $doc->clientDebt->reference_id
        ];
        $html = $this->templateStr($html, $doc, $modifieds);
        $html = $this->templateStr($html, $user);
        break;
    }
    
    return $html;
  }
}
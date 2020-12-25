<?php
namespace App\Helpers;

class TemplateHelpers
{
  public function templateStr($html, $object, $modified=[])
  {
    preg_match_all("/\<\!\-\-([a-z_]+)\-\-\>/", $html, $outputs);
    $string = $html;
    foreach($outputs[1] as $k => $output){
        foreach ($modified as $key => $value) {
          $object[$key] = $value;
        }
        $replaced = str_replace($outputs[0][$k], $object[$output], $string);
        $string = $replaced;
    }
    return $string;
  }

  public function switchDisplay($html, $ifs){
    preg_match_all("/(?:\<\!\-\-(if)\(([a-z_]+)\)\-\-\>)((.|\n)*?)(?:\<\!\-\-(endif)\(([a-z_]+)\)\-\-\>)/", $html, $if_output);

    foreach ($if_output[2] as $key => $value) {
        if(!$ifs[$value] || $ifs[$value] && !$ifs[$value]->count()){
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
        $loophtml .= $this->templateStr($looping[3][$key], $val);
      }
      $html = str_replace($looping[3][$key], $loophtml, $html);
    }

    return $html;
  }
}
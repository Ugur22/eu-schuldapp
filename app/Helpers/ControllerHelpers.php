<?php
namespace App\Helpers;

class ControllerHelpers
{
  public function myClient($me, $client_id)
  {
    $myClients = $me->consultant->clients()->pluck('id')->toArray();
    return in_array($client_id, $myClients) ? true: false;
  }
}
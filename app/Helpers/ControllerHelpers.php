<?php
namespace App\Helpers;
use App\Models\Debt;
use App\Models\Client;
use App\Models\Income;
use App\Models\Outcome;

class ControllerHelpers
{
  public function myClient($me, $client_id)
  {
    $myClients = $me->consultant->clients()->pluck('id')->toArray();
    return in_array($client_id, $myClients) ? true: false;
  }

  public function debtTable($client_id)
  {
    $debts = Debt::where('client_id', $client_id)->get();
    $table = '';
    $total_debt = 0;
    $total_total_redeemed = 0;
    $total_redeem_permonth = 0;
    $total_total_redemption = 0;
    foreach ($debts as $key => $debt) {
      $table .= '<tr>
        <td>'. $debt->reference_id .'</td>
        <td>'. $debt->debtor->name .'</td>
        <td>HI</td>
        <td>'. $debt->terms .'</td>
        <td>'. number_format($debt->percentage, 2, '.', ',') .'</td>
        <td>'. number_format($debt->debt_amount, 2, '.', ',') .'</td>
        <td>'. number_format($debt->total_redeemed, 2, '.', ',') .'</td>
        <td>'. number_format($debt->redeem_per_month, 2, '.', ',') .'</td>
        <td>'. number_format($debt->total_redemption, 2, '.', ',') .'</td>
      </tr>';
      $total_debt = $total_debt + $debt->debt_amount;
      $total_total_redeemed = $total_total_redeemed + $debt->total_redeemed;
      $total_redeem_permonth = $total_redeem_permonth + $debt->redeem_per_month;
      $total_total_redemption = $total_total_redemption + $debt->total_redemption;
    }
    $table .= '<tr>
      <td colspan="5" class="grand-summary">&nbsp;</td>
      <td class="grand-summary">'. number_format($total_debt, 2, '.', ',') .'</td>
      <td class="grand-summary">'. number_format($total_total_redeemed, 2, '.', ',') .'</td>
      <td class="grand-summary">'. number_format($total_redeem_permonth, 2, '.', ',') .'</td>
      <td class="grand-summary">'. number_format($total_total_redemption, 2, '.', ',') .'</td>
    </tr>';

    return $table;
  }

  public function incomesTable($client_id)
  {
    $results = [];
    $incomes = Income::orderBy('sort')->get();
    $results['client'] = [];
    $results['partner'] = [];
    $total_income_client = 0;
    $total_income_partner = 0;
    $table = '';
    $client = Client::find($client_id);
    foreach ($incomes as $key => $c_income) {
      if($key == 0){
        $table .= '<tr>';
        $table .= '<td colspan="3" class="th">Client</td>';
        $table .= '</tr>';
      }
      $clientIncome = $c_income->client()->where('client_id', $client_id)->where('client_type', 'client')->first();
      $income_client = $clientIncome ? $clientIncome->amount: 0;
      $table .= '<tr>';
      $table .= '<td>'. $c_income->name .'</td>';
      $table .= '<td>'. ($clientIncome && $clientIncome->employer ? $clientIncome->employer->name: '-') .'</td>';
      $table .= '<td align="right">'. number_format($income_client, 2, '.', ',') .'</td>';
      $table .= '</tr>';
      $total_income_client = $income_client + $total_income_client;
    }
    $table .= '<tr><td colspan="2" class="group"></td><td class="group" align="right">'. number_format($total_income_client, 2, '.', ',') .'</td></tr>';
    if($client->partner_lastname){
      foreach ($incomes as $key => $p_income) {
        if($key == 0){
          $table .= '<tr>';
          $table .= '<td colspan="3" class="th">Partner</td>';
          $table .= '</tr>';
        }
        $partnerIncome = $p_income->client()->where('client_id', $client_id)->where('client_type', 'partner')->first();
        $income_partner = $partnerIncome ? $partnerIncome->amount: 0;
        $table .= '<tr>';
        $table .= '<td>'. $p_income->name .'</td>';
        $table .= '<td>'. ($partnerIncome && $partnerIncome->employer ? $partnerIncome->employer->name: '-') .'</td>';
        $table .= '<td align="right">'. number_format($income_partner, 2, '.', ',') .'</td>';
        $table .= '</tr>';
        $total_income_partner = $income_partner + $total_income_partner;
      }
      $table .= '<tr><td colspan="2" class="group"></td><td class="group" align="right">'. number_format($total_income_partner, 2, '.', ',') .'</td></tr>';
    }
    $total = $total_income_client + $total_income_partner;
    $table .= '<tr><td colspan="2" class="grand-summary"></td><td align="right" class="grand-summary">'. number_format($total, 2, '.', ',') .'</td></tr>';

    return $table;
  }

  public function outcomesTable($client_id)
  {
    $results = [];
    $outcomes = Outcome::orderBy('sort')->get();
    $results['client'] = [];
    $results['partner'] = [];
    $total_outcome_client = 0;
    $total_outcome_partner = 0;
    $table = '';
    $client = Client::find($client_id);
    foreach ($outcomes as $key => $c_outcome) {
      if($key == 0){
        $table .= '<tr>';
        $table .= '<td colspan="4" class="th">Client</td>';
        $table .= '</tr>';
      }
      $clientOutcome = $c_outcome->client()->where('client_id', $client_id)->where('client_type', 'client')->first();
      $outcome_client = $clientOutcome ? $clientOutcome->amount: 0;
      $table .= '<tr>';
      $table .= '<td>'. $c_outcome->name .'</td>';
      $table .= '<td>'. ($clientOutcome && $clientOutcome->company ? $clientOutcome->company->name: '-') .'</td>';
      $table .= '<td>'. ($clientOutcome ? $clientOutcome->client_number: '-') .'</td>';
      $table .= '<td align="right">'. number_format($outcome_client, 2, '.', ',') .'</td>';
      $table .= '</tr>';
      $total_outcome_client = $outcome_client + $total_outcome_client;
    }
    $table .= '<tr><td colspan="3" class="group"></td><td class="group" align="right">'. number_format($total_outcome_client, 2, '.', ',') .'</td></tr>';
    if($client->partner_lastname){
      foreach ($outcomes as $key => $p_outcome) {
        if($key == 0){
          $table .= '<tr>';
          $table .= '<td colspan="4" class="th">Partner</td>';
          $table .= '</tr>';
        }
        $partnerOutcome = $p_outcome->client()->where('client_id', $client_id)->where('client_type', 'partner')->first();
        $outcome_partner = $partnerOutcome ? $partnerOutcome->amount: 0;
        $table .= '<tr>';
        $table .= '<td>'. $p_outcome->name .'</td>';
        $table .= '<td>'. ($partnerOutcome && $partnerOutcome->company ? $partnerOutcome->company->name: '-') .'</td>';
        $table .= '<td>'. ($clientOutcome ? $clientOutcome->client_number: '-') .'</td>';
        $table .= '<td align="right">'. number_format($outcome_partner, 2, '.', ',') .'</td>';
        $table .= '</tr>';
        $total_outcome_partner = $outcome_partner + $total_outcome_partner;
      }
      $table .= '<tr><td colspan="3" class="group"></td><td class="group" align="right">'. number_format($total_outcome_partner, 2, '.', ',') .'</td></tr>';
    }
    $total = $total_outcome_client + $total_outcome_partner;
    $table .= '<tr><td colspan="3" class="grand-summary"></td><td align="right" class="grand-summary">'. number_format($total, 2, '.', ',') .'</td></tr>';

    return $table;
  }

}
<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\JWTAuth;
use App\Models\Client;
use App\Models\Appointment;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt, Request $request)
    {
        $this->jwt = $jwt;
    }

    public function appointments(JWTAuth $jwt)
    {
        $role = $this->jwt->user()->role->slug;
        $appointments = Appointment::orderBy('event_date');
        if($role == 'client'){
            $appointments = $appointments->where('client_id', $this->jwt->user()->id);
        }else{
            $appointments = $appointments->where('consultant_id', $this->jwt->user()->id);
        }
        $dates = $appointments->pluck('event_date')->toArray();
        $ids = $appointments->pluck('id')->toArray();
        $calendars = [];
        foreach ($dates as $k=>$event) {
            $date = \Carbon\Carbon::parse($event)->format('Y-m-d');
            $cals = Appointment::where('event_date', '<=', $date.' 23:59:59')->where('event_date', '>=', $date.' 00:00:00')->whereIn('id', $ids)->get();
            foreach($cals as $key=>$cal){
                $time = \Carbon\Carbon::parse($cal->event_date)->format('H:i');
                $notes = $cal->notes;
                $consultant = trim($cal->consultant->firstname.' '.$cal->consultant->lastname);
                $client = trim($cal->client->firstname.' '.$cal->client->lastname);
                $calendars[$date][$key] = [
                    'time' => $time,
                    'notes' => $notes,
                    'consultant' => $consultant,
                    'client' => $client,
                ];
            }
        }
        
        if(count($calendars)){
            return response()->json(['success' => true, 'results' => $calendars]);
        }else{
            return response()->json(['success' => false, 'message' => 'no income']);
        }
    }

    
}

<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\JWTAuth;
use App\Models\Client;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Mail\Appointments;
use Illuminate\Support\Facades\Mail;

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

    public function appointments()
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

    public function list(Request $request)
    {
        $items = $this->jwt->user()->consultant->appointments()->orderBy('event_date')->get();
        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_appointment']);
        }
    }

    public function show($id)
    {
        $role = $this->jwt->user()->role->slug;
        if ($role != 'consultant') {
            return response()->json(['success' => false, 'message' => 'consultant only']);
        }
        $appointment = Appointment::find($id);
        
        if($appointment){
            return response()->json(['success' => true, 'results' => $appointment]);
        }else{
            return response()->json(['success' => false, 'message' => 'no appointment found']);
        }
    }

    public function read(Request $request)
    {
        $input = $request->all();
        $item = Appointment::with('location')->with('client')->orderBy('event_date');
        if(isset($input['id'])){
            $item = $item->whereId($input['id'])->first();
        }else{
            if(isset($input['event_date'])){
                $item = $item->where('event_date', '>=', \Carbon\Carbon::parse($input['event_date'])->format('Y-m-d 00:00:00'))->where('event_date', '<=', \Carbon\Carbon::parse($input['event_date'])->format('Y-m-d 23:59:59'));
            }elseif(isset($input['event_from']) && isset($input['event_to'])){
                $item = $item->where('event_date', '>=', \Carbon\Carbon::parse($input['event_from'])->format('Y-m-d 00:00:00'))->where('event_date', '<=', \Carbon\Carbon::parse($input['event_to'])->format('Y-m-d 23:59:59'));
            }
            $item = $item->where('consultant_id', $this->jwt->user()->id)->get();
        }
        if($item){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'not_found']);
        }
    }

    public function create(Request $request)
    {
        $role = $this->jwt->user()->role->slug;
        $input = $request->all();

        if ($role == 'consultant') {
            $item = new Appointment;
            $item->event_date = \Carbon\Carbon::parse($input['date'].' '.$input['time']);
            $item->client_id = $input['client_id'];
            $item->location_id = $input['location_id'];
            $item->consultant_id = $this->jwt->user()->id;
            $item->notes = $input['notes'];
            $item->status = 'pending';
    
            if($item->save()){
                Mail::to($item->client->user->email)->send(new Appointments($item));
                return response()->json(['success' => true, 'message' => 'appointment made', 'results' => $item]);
            }else{
                return response()->json(['success' => false, 'message' => 'make appointment failed']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'consultant only']);
        }
    }

    public function update(Request $request, $id)
    {
        $role = $this->jwt->user()->role->slug;
        $input = $request->all();

        if ($role == 'consultant') {
            $item = Appointment::find($id);
            $item->event_date = \Carbon\Carbon::parse($input['date'].' '.$input['time']);
            $item->location_id = $input['location_id'];
            $item->notes = $input['notes'];
            $item->status = $input['status'];
        } else {
            return response()->json(['success' => false, 'message' => 'consultant only']);
        }

        if($item->save()){
            /* Mail::to($item->client->user->email)->send(new Appointments($item)); */
            $results = Appointment::with('client')->with('location')->where('event_date', '>=', \Carbon\Carbon::parse($item->event_date)->format('Y-m-d 00:00:00'))->where('event_date', '<=', \Carbon\Carbon::parse($item->event_date)->format('Y-m-d 23:59:59'))->get();
            return response()->json(['success' => true, 'message' => 'appointment updated', 'results' => $results]);
        }else{
            return response()->json(['success' => false, 'message' => 'update failed']);
        }
    }

    public function destroy($id)
    {
        $appointment = Appointment::find($id);
        
        if($appointment->delete()){
            return response()->json(['success' => true, 'message' => 'appointment deleted']);
        }else{
            return response()->json(['success' => false, 'message' => 'no income']);
        }
    }
}

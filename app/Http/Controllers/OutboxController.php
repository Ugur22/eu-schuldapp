<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Outbox;
use Tymon\JWTAuth\JWTAuth;

class OutboxController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;
    protected $consultant = false;

    public function __construct(JWTAuth $jwt, Request $request)
    {
        $this->jwt = $jwt;
        $role = $this->jwt->user()->role->slug;
        if ($role == 'consultant') {
          $this->consultant = true;
        }
    }

    public function show(Request $request)
    {
      if (!$this->consultant) {
        return response()->json(['success' => false, 'message' => 'no access']);
      }

      $input = $request->all();
      $outbox = Outbox::whereId($input['id'])->where('client_id', $input['client_id'])->with('documents')->with('client')->with('status')->with('debt')->first();

      if ($outbox) {
        return response()->json(['success' => true, 'results' => $outbox]);
      } else {
        return response()->json(['success' => false, 'message' => 'not found']);
      }
    }

    public function outboxes(Request $request)
    {
      if (!$this->consultant) {
        return response()->json(['success' => false, 'message' => 'no access']);
      }
      
      $input = $request->all();
      $outboxes = Outbox::with('documents')->with('client')->with('status')->with('debt')->orderBy('created_at');
      if (isset($input['debt_id'])) {
        $outboxes = $outboxes->where('client_debt_id', $input['debt_id']);
      }
      if ($input['client_id']) {
        $outboxes = $outboxes->where('client_id', $input['client_id']); 
      }
      $outboxes = $outboxes->get();

      if ($outboxes->count()) {
        return response()->json(['success' => true, 'results' => $outboxes]);
      } else {
        return response()->json(['success' => false, 'message' => 'no outbox']);
      }
    }
}

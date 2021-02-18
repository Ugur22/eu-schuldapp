<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tymon\JWTAuth\JWTAuth;
use App\Models\User;
use App\Models\Client;
use App\Models\Consultant;

class AuthController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    public function updateToken()
    {
        $user_id = $this->jwt->user()->id;
        $token = Hash::make($user_id.\Carbon\Carbon::now()->timestamp);
        $user = User::find($user_id);
        $user->download_token = $token;
        if($user->save()){
            return response()->json(['success' => true, 'token' => $token]);
        }else{
            return response()->json(['success' => false, 'message' => 'update failed']);
        }
    }

    public function consultantRegistration(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|min:10|max:255',
            'password' => 'required',
            'password_confirm' => 'required|same:password',
            'place_id' => 'required',
            'firstname' => 'required|min:1|max:255',
            'lastname' => 'required|min:2|max:255'
        ]);

        try {
          $input = $request->all();

          $user = new User;
          $user->email = $input['email'];
          $user->password = Hash::make($input['password']);
          $user->role_id = 2;
  
          if($user->save()){
              $consultant = new Consultant;
              $consultant->user_id = $user->id;
              $consultant->firstname = $input['firstname'];
              $consultant->lastname = $input['lastname'];
              $consultant->gender = $input['gender'];
              $consultant->phonenumber = $input['phonenumber'];
              $consultant->company_name = $input['company_name'];
              $consultant->place_id = $input['place_id'];
              if($consultant->save()) {
                  return response()->json(['success' => true]);
              }else{
                  return response()->json(['success' => false, 'message' => 'register_failed']);
              }
          }else{
            return response()->json(['success' => false, 'message' => 'register_failed']);
          }
  
        } catch (\Exception $e) {
          return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);

        try {
            $token = $this->jwt->attempt($request->only('email', 'password'));
            if (!$token) {
                return response()->json(['user_not_found'], 404);
            }else{
                $user = User::with('role')->with('client')->with('consultant')->find($this->jwt->user()->id);
                $user->last_login = \Carbon\Carbon::now();
                $user->save();
                return response()->json(['success' => true, 'user' => $user, 'token' => compact('token')]);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent' => $e->getMessage()], 500);

        }
        // return $this->jwt->user()->role;
        // return response()->json(compact('token'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tymon\JWTAuth\JWTAuth;
use App\Models\User;
use App\Models\Client;

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

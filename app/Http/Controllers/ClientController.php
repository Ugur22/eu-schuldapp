<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    function index()
    {
        return Client::all();
        // dd(Hash::make("Test123"));
    }
}

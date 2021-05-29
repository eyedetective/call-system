<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InboundController extends Controller
{
    public function index()
    {
        $users = \App\User::all();
        return view('inbound', ['users'=>$users]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InboundController extends Controller
{
    public function index()
    {
        $users = \App\User::all();
        $topics = \App\Topic::all();
        return view('inbound', compact('users','topics'));
    }
}

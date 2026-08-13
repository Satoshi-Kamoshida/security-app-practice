<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ContactController extends Controller
{
    public function form()
    {
        $users = User::all();
        return view("contact.form", compact("users"));
    }

    public function thanks()
    {
        $users = User::all();
        return view("contact.thanks", compact("users"));
    }

    public function store(Request $request)
    {
        return view("contact.thanks",[
            "name" => $request->name,
            "email" => $request->email,
            "message"=> $request->message,
        ]);
    }

}

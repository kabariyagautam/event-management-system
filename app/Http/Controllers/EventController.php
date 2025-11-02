<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        return view('events.index');
    }

    public function admin()
    {
        return view('admin.index');
    }

    public function edit()
    {
        return view('admin.edit');
    }

}

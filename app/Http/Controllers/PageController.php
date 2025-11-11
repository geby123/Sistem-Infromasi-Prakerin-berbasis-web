<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index() {
        return view('index');
    }
    public function about() {
        return view('about');
    }

    public function contact() {
        return view('contact');
    }

    public function team() {
        return view('team');
    }
}

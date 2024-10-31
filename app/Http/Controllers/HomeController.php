<?php

namespace App\Http\Controllers;

use App\Models\Videos;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $videos = Videos::where('status', 'public')->get();
        return view('home', compact('videos'));
    }
}

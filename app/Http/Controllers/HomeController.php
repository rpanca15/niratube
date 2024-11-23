<?php

namespace App\Http\Controllers;

use App\Models\Videos;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $videos = Videos::where('privacy', 'public')->get();
        return view('home', compact('videos'));
    }
}

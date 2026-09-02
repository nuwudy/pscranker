<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;

class OmrController extends Controller
{
    public function index()
    {
        $quiz = Quiz::with('questions')->where('slug', '3-min-rapid-blitz')->first() 
            ?? Quiz::with('questions')->first();

        return view('pages.omr', compact('quiz'));
    }
}

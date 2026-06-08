<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DetectionController extends Controller
{
    public function showForm()
    {
        return view('detection.form');
    }

    public function analyze(Request $request)
    {
        // Unit 05 will implement this
    }

    public function showResult()
    {
        return view('detection.result');
    }
}

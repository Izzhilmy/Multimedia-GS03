<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function index()
    {
        $results = DB::connection('mysql')
            ->table('student_detection_summary')
            ->orderByDesc('detected_at')
            ->paginate(10);

        return view('history.index', compact('results'));
    }
}

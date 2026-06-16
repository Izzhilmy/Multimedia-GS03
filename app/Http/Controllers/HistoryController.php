<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function index()
    {
        $matricNo = session('student.matric_no');

        $results = DB::connection('mysql')
            ->table('student_detection_summary')
            ->where('matric_no', $matricNo)
            ->orderByDesc('detected_at')
            ->paginate(10);

        return view('history.index', compact('results'));
    }
}

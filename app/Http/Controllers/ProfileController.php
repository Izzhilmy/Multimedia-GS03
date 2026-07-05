<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function show()
    {
        $matricNo = session('student.matric_no');

        $student = DB::connection('mmdb')
            ->table('vstu')
            ->whereRaw('UPPER(matric_no) = ?', [strtoupper($matricNo)])
            ->first();

        return view('profile.show', compact('student'));
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AuthService
{
    public function execute(string $matricNo, string $password): ?object
    {
        $student = DB::connection('mmdb')
            ->table('vstu')
            ->whereRaw('UPPER(matric_no) = ?', [strtoupper($matricNo)])
            ->first();

        if (!$student || empty($student->password)) {
            return null;
        }

        if ($student->password !== $password) {
            return null;
        }

        return $student;
    }
}

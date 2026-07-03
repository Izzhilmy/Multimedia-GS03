<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AuthService
{
    public function execute(string $matricNo, string $password): ?object
    {
        $student = DB::connection('mmdb')
            ->table('vstu')
            ->where('matric_no', $matricNo)
            ->first();

        if (!$student || $student->password === null) {
            return null;
        }

        if ($student->password !== $password) {
            return null;
        }

        return $student;
    }
}

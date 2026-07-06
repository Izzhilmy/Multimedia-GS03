<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function execute(string $matricNo, string $password): array
    {
        try {
            $student = DB::connection('mmdb')
                ->table('vstu')
                ->whereRaw('UPPER(matric_no) = ?', [strtoupper($matricNo)])
                ->first();
        } catch (\Throwable $e) {
            Log::error('mmdb connection failed: ' . $e->getMessage());
            return ['student' => null, 'error' => 'Database connection error. Please contact the administrator.'];
        }

        if (!$student) {
            return ['student' => null, 'error' => 'Matric number not found.'];
        }

        if (empty($student->password)) {
            return ['student' => null, 'error' => 'This account has no password set. Please contact the administrator.'];
        }

        if ($student->password !== $password) {
            return ['student' => null, 'error' => 'Incorrect password.'];
        }

        return ['student' => $student, 'error' => null];
    }
}

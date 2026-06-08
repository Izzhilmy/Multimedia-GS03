<?php

namespace App\Services;

class AbrService
{
    public function execute(string $icNumber): array
    {
        $digits = preg_replace('/\D/', '', $icNumber);

        if (strlen($digits) !== 12) {
            return ['prediction' => 'Unknown', 'detail' => ['ic_gender' => 'Unknown']];
        }

        $lastDigit = (int) substr($digits, -1);
        $gender    = ($lastDigit % 2 !== 0) ? 'Male' : 'Female';

        return [
            'prediction' => $gender,
            'detail'     => ['ic_gender' => $gender, 'last_digit' => $lastDigit],
        ];
    }
}

<?php

namespace App\Services;

class CbrService
{
    public function execute(string $gender, int $confidence): array
    {
        return [
            'prediction' => $gender,
            'detail'     => ['confidence' => $confidence],
        ];
    }
}

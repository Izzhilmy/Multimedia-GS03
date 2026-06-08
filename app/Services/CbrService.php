<?php

namespace App\Services;

class CbrService
{
    public function execute(string $hairLength, bool $isHijab, bool $hasFacialHair): array
    {
        $scores = ['Male' => 0, 'Female' => 0];

        if ($isHijab)       { $scores['Female'] += 3; }
        if ($hasFacialHair) { $scores['Male']   += 3; }

        match (strtolower($hairLength)) {
            'long'   => $scores['Female'] += 2,
            'short'  => $scores['Male']   += 2,
            'medium' => $scores['Female'] += 1,
            default  => null,
        };

        $prediction = $scores['Female'] >= $scores['Male'] ? 'Female' : 'Male';
        $total      = array_sum($scores);
        $confidence = $total > 0
            ? (int) round(($scores[$prediction] / $total) * 100)
            : 50;

        return [
            'prediction' => $prediction,
            'detail'     => [
                'hair_length'     => $hairLength,
                'is_hijab'        => $isHijab,
                'has_facial_hair' => $hasFacialHair,
                'confidence'      => $confidence,
            ],
        ];
    }
}

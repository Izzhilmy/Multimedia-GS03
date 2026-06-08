<?php

namespace App\Services;

class DetectionFusionService
{
    public function execute(string $abrResult, string $tbrResult, string $cbrResult): array
    {
        $votes = array_filter(
            [$abrResult, $tbrResult, $cbrResult],
            fn($v) => in_array($v, ['Male', 'Female'])
        );

        $counts = array_count_values($votes);
        $male   = $counts['Male']   ?? 0;
        $female = $counts['Female'] ?? 0;
        $total  = count($votes);

        if ($total === 0) {
            $final      = 'Unknown';
            $confidence = 0;
        } elseif ($female > $male) {
            $final      = 'Female';
            $confidence = (int) round(($female / $total) * 100);
        } else {
            $final      = 'Male';
            $confidence = (int) round(($male / $total) * 100);
        }

        return [
            'abr_result'   => $abrResult,
            'tbr_result'   => $tbrResult,
            'cbr_result'   => $cbrResult,
            'final_gender' => $final,
            'confidence'   => $confidence,
        ];
    }
}

<?php

namespace App\Services;

class TbrService
{
    private array $maleKeywords   = ['bin', 'mr', 'mr.', 'encik', 'en.', 'tuan'];
    private array $femaleKeywords = ['binti', 'bte', 'bt', 'mrs', 'mrs.', 'miss',
                                     'ms', 'ms.', 'puan'];

    public function execute(string $fullName, string $honorific = ''): array
    {
        $text  = strtolower(trim($honorific . ' ' . $fullName));
        $words = preg_split('/\s+/', $text);

        foreach ($this->femaleKeywords as $kw) {
            if (in_array($kw, $words) || str_contains($text, $kw)) {
                return [
                    'prediction' => 'Female',
                    'detail'     => ['honorific' => $honorific, 'keyword' => $kw],
                ];
            }
        }

        foreach ($this->maleKeywords as $kw) {
            if (in_array($kw, $words) || str_contains($text, $kw)) {
                return [
                    'prediction' => 'Male',
                    'detail'     => ['honorific' => $honorific, 'keyword' => $kw],
                ];
            }
        }

        return [
            'prediction' => 'Unknown',
            'detail'     => ['honorific' => $honorific, 'keyword' => null],
        ];
    }
}

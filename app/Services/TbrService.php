<?php

namespace App\Services;

class TbrService
{
    private array $femaleName  = ['binti', 'bte', 'bt'];
    private array $maleName    = ['bin'];
    private array $femaleTitle = ['mrs', 'mrs.', 'miss', 'ms', 'ms.', 'puan'];
    private array $maleTitle   = ['mr', 'mr.', 'encik', 'en.', 'tuan'];

    public function execute(string $fullName, string $honorific = ''): array
    {
        $text  = strtolower(trim($fullName));
        $words = preg_split('/\s+/', $text);

        $nameKeyword  = $this->findKeyword($words, $text, array_merge($this->femaleName, $this->maleName));
        $titleKeyword = $honorific !== ''
            ? strtolower(trim($honorific))
            : $this->findKeyword($words, $text, array_merge($this->femaleTitle, $this->maleTitle));

        return [
            'prediction' => $this->predict($nameKeyword, $titleKeyword),
            'detail'     => [
                'honorific'     => $honorific,
                'name_keyword'  => $nameKeyword,
                'title_keyword' => $titleKeyword,
            ],
        ];
    }

    private function findKeyword(array $words, string $text, array $keywords): ?string
    {
        foreach ($keywords as $kw) {
            if (in_array($kw, $words) || str_contains($text, $kw)) {
                return $kw;
            }
        }
        return null;
    }

    private function predict(?string $nameKeyword, ?string $titleKeyword): string
    {
        if ($nameKeyword !== null && in_array($nameKeyword, $this->femaleName)) return 'Female';
        if ($titleKeyword !== null && in_array($titleKeyword, $this->femaleTitle)) return 'Female';
        if ($nameKeyword !== null && in_array($nameKeyword, $this->maleName))   return 'Male';
        if ($titleKeyword !== null && in_array($titleKeyword, $this->maleTitle)) return 'Male';
        return 'Unknown';
    }
}

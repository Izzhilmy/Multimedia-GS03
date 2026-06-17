<?php

namespace App\Services;

class TbrService
{
    private array $femaleName  = ['binti', 'bte', 'bt'];
    private array $maleName    = ['bin'];
    private array $femaleTitle = ['mrs', 'mrs.', 'miss', 'ms', 'ms.', 'puan'];
    private array $maleTitle   = ['mr', 'mr.', 'encik', 'en.', 'tuan'];

    private array $maleFirstNames = [
        'ahmad', 'muhammad', 'mohamad', 'mohamed', 'muhamad', 'ali', 'hassan',
        'hussein', 'ibrahim', 'ismail', 'omar', 'osman', 'aziz', 'zainal',
        'hafiz', 'farid', 'amirul', 'hakim', 'irfan', 'khairul', 'luqman',
        'amin', 'syafiq', 'ridhwan', 'izzat', 'ikhwan', 'helmi', 'haziq',
        'haris', 'azri', 'azlan', 'azwan', 'fadzil', 'rizal', 'nadzri',
    ];

    private array $femaleFirstNames = [
        'siti', 'nur', 'nurul', 'noor', 'nora', 'noraini', 'fatimah',
        'aishah', 'aisha', 'khadijah', 'mariam', 'maryam', 'zainab',
        'halimah', 'rohani', 'hasnah', 'normala', 'aminah', 'nabilah',
        'nadia', 'farhana', 'hidayah', 'izzati', 'liyana', 'yasmin',
        'zulaikha', 'zuraidah', 'nadhirah', 'syazwani', 'wardah', 'hanis',
    ];

    public function execute(string $fullName, string $honorific = ''): array
    {
        $text  = strtolower(trim($fullName));
        $words = preg_split('/\s+/', $text);

        $nameKeyword  = $this->findKeyword($words, $text, array_merge($this->femaleName, $this->maleName));
        $titleKeyword = $honorific !== ''
            ? strtolower(trim($honorific))
            : $this->findKeyword($words, $text, array_merge($this->femaleTitle, $this->maleTitle));
        $firstNameHit = $this->matchFirstName($words);

        return [
            'prediction' => $this->predict($nameKeyword, $titleKeyword, $firstNameHit),
            'detail'     => [
                'honorific'     => $honorific,
                'name_keyword'  => $nameKeyword,
                'title_keyword' => $titleKeyword,
                'first_name'    => $firstNameHit,
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

    private function matchFirstName(array $words): ?string
    {
        $skip = array_merge($this->femaleTitle, $this->maleTitle, $this->femaleName, $this->maleName);
        $all  = array_merge($this->maleFirstNames, $this->femaleFirstNames);

        foreach ($words as $word) {
            $clean = rtrim($word, '.,');
            if (in_array($clean, $skip)) continue;
            if (in_array($clean, $all)) return $clean;
        }
        return null;
    }

    private function predict(?string $nameKeyword, ?string $titleKeyword, ?string $firstNameHit): string
    {
        if ($nameKeyword  !== null && in_array($nameKeyword,  $this->femaleName))      return 'Female';
        if ($titleKeyword !== null && in_array($titleKeyword, $this->femaleTitle))     return 'Female';
        if ($nameKeyword  !== null && in_array($nameKeyword,  $this->maleName))        return 'Male';
        if ($titleKeyword !== null && in_array($titleKeyword, $this->maleTitle))       return 'Male';
        if ($firstNameHit !== null && in_array($firstNameHit, $this->femaleFirstNames)) return 'Female';
        if ($firstNameHit !== null && in_array($firstNameHit, $this->maleFirstNames))   return 'Male';
        return 'Unknown';
    }
}

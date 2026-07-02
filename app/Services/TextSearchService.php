<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TextSearchService
{
    private array $femaleWords  = ['female', 'woman', 'girl', 'perempuan', 'wanita'];
    private array $maleWords    = ['male', 'man', 'boy', 'lelaki'];

    private array $hijabPhrases   = ['wearing hijab', 'with hijab', 'pakai tudung', 'bertudung'];
    private array $hijabWords     = ['hijab', 'tudung', 'scarf'];
    private array $noHijabPhrases = ['no hijab', 'without hijab', 'not wearing hijab', 'no tudung'];

    private array $beardPhrases   = ['facial hair', 'has beard', 'with beard'];
    private array $beardWords     = ['beard', 'mustache', 'janggut', 'misai'];
    private array $noBeardPhrases = ['no beard', 'no facial hair', 'clean shave', 'clean-shave', 'shaved'];

    private array $shortHair  = ['short hair', 'rambut pendek'];
    private array $longHair   = ['long hair', 'rambut panjang'];
    private array $mediumHair = ['medium hair', 'medium length'];

    private array $stopWords = [
        'with', 'and', 'the', 'a', 'an', 'is', 'has', 'who', 'named',
        'wearing', 'wears', 'have', 'about', 'person', 'people', 'someone',
    ];

    public function search(string $description, string $matricNo)
    {
        $text  = strtolower(trim($description));
        $words = preg_split('/\s+/', $text);

        $gender    = $this->extractGender($words);
        $isHijab   = $this->extractHijab($text);
        $hair      = $this->extractHair($text);
        $hasFacial = $this->extractFacialHair($text);
        $nameHint  = $this->extractNameHint($words, $text);

        $query = DB::connection('mysql')
            ->table('student_detection_summary')
            ->where('matric_no', $matricNo);

        if ($gender    !== null) $query->where('final_gender',      $gender);
        if ($isHijab   !== null) $query->where('is_hijab_detected', $isHijab);
        if ($hair      !== null) $query->where('hair_feature',      $hair);
        if ($hasFacial !== null) $query->where('has_facial_hair',   $hasFacial);
        if ($nameHint  !== null) {
            $safe = str_replace(['%', '_'], ['\%', '\_'], $nameHint);
            $query->where('full_name', 'like', "%{$safe}%");
        }

        return $query->orderByDesc('detected_at')->paginate(10);
    }

    private function extractGender(array $words): ?string
    {
        foreach ($words as $w) {
            if (in_array($w, $this->femaleWords)) return 'Female';
            if (in_array($w, $this->maleWords))   return 'Male';
        }
        return null;
    }

    private function extractHijab(string $text): ?bool
    {
        foreach ($this->noHijabPhrases as $p) {
            if (str_contains($text, $p)) return false;
        }
        foreach (array_merge($this->hijabPhrases, $this->hijabWords) as $p) {
            if (str_contains($text, $p)) return true;
        }
        return null;
    }

    private function extractFacialHair(string $text): ?bool
    {
        foreach ($this->noBeardPhrases as $p) {
            if (str_contains($text, $p)) return false;
        }
        foreach (array_merge($this->beardPhrases, $this->beardWords) as $p) {
            if (str_contains($text, $p)) return true;
        }
        return null;
    }

    private function extractHair(string $text): ?string
    {
        foreach ($this->shortHair  as $p) { if (str_contains($text, $p)) return 'Short'; }
        foreach ($this->longHair   as $p) { if (str_contains($text, $p)) return 'Long'; }
        foreach ($this->mediumHair as $p) { if (str_contains($text, $p)) return 'Medium'; }
        return null;
    }

    private function extractNameHint(array $words, string $text): ?string
    {
        $visualPhrases = array_merge(
            $this->hijabPhrases, $this->noHijabPhrases, $this->hijabWords,
            $this->beardPhrases, $this->noBeardPhrases, $this->beardWords,
            $this->shortHair, $this->longHair, $this->mediumHair
        );

        $cleaned = $text;
        foreach ($visualPhrases as $p) {
            $cleaned = str_replace($p, ' ', $cleaned);
        }

        $ignore    = array_merge($this->femaleWords, $this->maleWords, $this->stopWords);
        $remaining = array_filter(
            preg_split('/\s+/', trim($cleaned)),
            fn($w) => !in_array($w, $ignore) && strlen($w) > 1
        );

        $hint = implode(' ', $remaining);
        return $hint !== '' ? $hint : null;
    }
}

<?php

namespace App\Services;

use App\Models\UserProfile;
use App\Models\IdCardInfo;
use App\Models\TextInfo;
use App\Models\ImageAnalysis;
use App\Models\DetectionResult;

class DetectionResultService
{
    public function execute(array $data, array $fusion): DetectionResult
    {
        $profile = UserProfile::firstOrCreate(
            ['matric_no' => $data['matric_no']],
            ['full_name' => $data['full_name']]
        );

        IdCardInfo::create([
            'user_profile_id' => $profile->id,
            'ic_gender'       => $data['abr']['detail']['ic_gender'],
            'abr_result'      => $fusion['abr_result'],
        ]);

        TextInfo::create([
            'user_profile_id' => $profile->id,
            'honorific_title' => $data['tbr']['detail']['honorific'] ?? null,
            'name_keyword'    => $data['tbr']['detail']['name_keyword'] ?? null,
            'tbr_result'      => $fusion['tbr_result'],
        ]);

        ImageAnalysis::create([
            'user_profile_id'   => $profile->id,
            'hair_feature'      => null,
            'is_hijab_detected' => false,
            'has_facial_hair'   => false,
            'confidence_score'  => $data['cbr']['detail']['confidence'],
            'cbr_result'        => $fusion['cbr_result'],
        ]);

        return DetectionResult::create([
            'user_profile_id'   => $profile->id,
            'full_name'         => $data['full_name'],
            'ic_number'         => $data['ic_number'],
            'image_path'        => $data['image_path'] ?? null,
            'hair_feature'      => null,
            'is_hijab_detected' => false,
            'has_facial_hair'   => false,
            'abr_result'        => $fusion['abr_result'],
            'tbr_result'        => $fusion['tbr_result'],
            'cbr_result'        => $fusion['cbr_result'],
            'final_gender'      => $fusion['final_gender'],
            'confidence'        => $fusion['confidence'],
        ]);
    }
}

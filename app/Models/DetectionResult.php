<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetectionResult extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'detection_results';
    protected $fillable   = [
        'user_profile_id', 'full_name', 'ic_number', 'image_path',
        'hair_feature', 'is_hijab_detected', 'has_facial_hair',
        'abr_result', 'tbr_result', 'cbr_result', 'final_gender', 'confidence',
    ];
}

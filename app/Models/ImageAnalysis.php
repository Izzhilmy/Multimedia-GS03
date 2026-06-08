<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageAnalysis extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'image_analysis';
    protected $fillable   = [
        'user_profile_id', 'hair_feature', 'is_hijab_detected',
        'has_facial_hair', 'confidence_score', 'cbr_result',
    ];
}

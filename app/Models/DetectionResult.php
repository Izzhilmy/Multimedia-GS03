<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetectionResult extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'detection_results';
    protected $fillable   = [
        'user_profile_id', 'abr_result', 'tbr_result',
        'cbr_result', 'final_gender', 'confidence',
    ];
}

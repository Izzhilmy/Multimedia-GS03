<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextInfo extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'text_info';
    protected $fillable   = ['user_profile_id', 'honorific_title', 'name_keyword', 'tbr_result'];
}

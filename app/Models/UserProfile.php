<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $connection = 'mysql';
    protected $fillable   = ['matric_no', 'full_name', 'ic_number', 'image_path'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StuUser extends Model
{
    protected $connection = 'mmdb';
    protected $table      = 'vstu';
    public    $timestamps = false;

    protected $fillable = [];
}

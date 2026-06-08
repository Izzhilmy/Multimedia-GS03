<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdCardInfo extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'id_card_info';
    protected $fillable   = ['user_profile_id', 'ic_gender', 'abr_result'];
}

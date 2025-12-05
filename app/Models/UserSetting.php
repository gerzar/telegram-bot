<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $fillable = ['chat_id', 'repetition_time', 'role', 'status','words_per_day'];
    public $timestamps = false;
}

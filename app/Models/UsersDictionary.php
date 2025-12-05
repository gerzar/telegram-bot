<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersDictionary extends Model
{
    protected $fillable = ['chat_id','word_id','last_check','success','fails','learned'];
    public $timestamps = false;

    public function word()
    {
        return $this->belongsTo(Dictionary::class);
    }
}

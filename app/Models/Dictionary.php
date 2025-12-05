<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dictionary extends Model
{
    protected $fillable = ['word','translation','definition','example','sound', 'popularity'];
    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->usersWords()->delete();
        });
    }

    public function usersWords()
    {
        return $this->hasMany(UsersDictionary::class, 'word_id');
    }

    public function setTranslationAttribute($value)
    {
        $this->attributes['translation'] = mb_strtolower($value);
    }

    public function setWordAttribute($value)
    {
        $this->attributes['word'] = mb_strtolower($value);
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XrContentsGenre extends model
{
    protected $table = 'xr_contents_genre';

    public function getContentsGenreGroupTexts()
    {
        return $this->hasOne(XrContentsGenreText::class,'contents_genre_id','contents_genre_id');
    }

    public function getContentsGenreGroupWithBothTexts(){
        return $this->hasMany(XrContentsGenreText::class,'contents_genre_id','contents_genre_id');
    }
}

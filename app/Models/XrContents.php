<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XrContents extends model
{
    protected $table = 'xr_contents';

    public function getContentsGenreGroup()
    {   // 장르 복수로 변경시 hasManyThrough로
        return $this->hasOneThrough(
            XrContentsGenre::class,
            XrContentsGenreGroup::class,
            'contents_id',
            'contents_genre_id',
            'contents_id',
            'contents_genre_id'
        );
    }

    public function getContentsSensor()
    {
        return $this->hasManyThrough(
            XrSensor::class,
            XrContentsSensor::class,
            'contents_id',
            'sensor_id',
            'contents_id',
            'sensor_id'
        );
    }

    public function getThemeContents(){
        return $this->hasManyThrough(
            XrTheme::class,
            XrThemeContents::class,
            'contents_id',
            'theme_id',
            'contents_id',
            'theme_id'
        );
    }

    public function getContentsText(){
        return $this->hasMany(XrContentsText::class,'contents_id','contents_id');
    }

    public function getContentsResource(){
        return $this->hasMany(XrContentsResource::class,'contents_id','contents_id');
    }

    public function getDevelopmentalElementGroup(){
        return $this->hasManyThrough(
            XrDevelopmentalElement::class,
            XrDevelopmentalElementGroup::class,
            'contents_id',
            'type_id',
            'contents_id',
            'developmental_element_type_id'
        );
    }
}

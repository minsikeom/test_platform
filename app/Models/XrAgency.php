<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XrAgency extends model
{
    protected $table = 'xr_agency';

    public function getUserInfo()
    {
        return $this->belongsTo(XrUser::class,'agency_id','agency_id');
    }

    public function getAgencyWithGroupInfo()
    {
        return $this->hasManyThrough(
            XrGroup::class,
            XrUser::class,
            'agency_id',
            'group_id',
            'agency_id',
            'group_id'
        );
    }

}

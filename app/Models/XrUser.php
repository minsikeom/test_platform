<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class XrUser extends Authenticatable
{
    protected $table = 'xr_user';

    public $primaryKey = 'user_id';

    protected $fillable = [
        'login_id',
        'user_id',
        'group_id',
        'agency_id',
        'ut_code'
    ];

    protected $guarded = [
        'login_id', 'remember_token'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getAgencyInfo():hasone
    {
        return $this->hasone(XrAgency::class,'agency_id','agency_id');
    }

    public function getGroupInfo():hasone
    {
        return $this->hasone(XrGroup::class,'group_id','group_id');
    }


}

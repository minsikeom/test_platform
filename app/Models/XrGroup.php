<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XrGroup extends model
{
    protected $table = 'xr_group';

    // hasmanyThough 키 제거
    protected $hidden = [
        'laravel_through_key'
    ];
}

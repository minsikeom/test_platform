<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XrSensor extends model
{
    protected $table = 'xr_sensor';

    public function getSensorTypeInfo(){
        return $this->hasOne(XrSensorType::class,'sensor_type_id','sensor_type_id');
    }
}

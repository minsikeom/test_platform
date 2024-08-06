<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XrSensorType extends model
{
    protected $table = 'xr_sensor_type';

    public function getSensorList(){
        return $this->hasMany(XrSensor::class,'sensor_type_id','sensor_type_id');
    }
}

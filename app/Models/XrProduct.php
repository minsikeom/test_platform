<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XrProduct extends model
{
    protected $table = 'xr_product';

    public function getSensorGroupWithSensorInfo(){
        return $this->hasManyThrough(
            XrSensor::class,
            XrSensorGroup::class,
            'product_id',
            'sensor_id',
            'product_id',
            'sensor_id'
            );
    }

}

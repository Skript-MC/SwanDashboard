<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Config extends Model
{
    protected $collection = 'dash-config';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'value',
    ];

}

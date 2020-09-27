<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class CommandStats extends Model
{
    protected $collection = 'commandstats';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'commandId', 'uses',
    ];

}

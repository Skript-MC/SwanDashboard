<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MessageHistory extends Model
{

    protected $collection = 'history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'messageId', 'userId', 'userName', 'channelId', 'channelName', 'content',
    ];

}

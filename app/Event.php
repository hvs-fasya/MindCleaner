<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'description','user_id'
    ];

    /**
    * Get the user that owns the event.
    */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}

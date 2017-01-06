<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'description','user_id', 'sphere_id'
    ];

    /**
    * Get the user that owns the event.
    */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get the sphere which event belongs to.
     */
    public function sphere()
    {
        return $this->belongsTo('App\Sphere');
    }

    /**
     * The EventTypes that belong to the event.
     */
    public function roles()
    {
        return $this->belongsToMany('App\EventType');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sphere extends Model
{
    protected $fillable = [
        'description','user_id'
    ];

    /**
     * Get the events for the sphere.
     */
    public function events()
    {
        return $this->hasMany('App\Event');
    }

    public function is_common()
    {
        if(is_null($this->user_id)){
            return true;
        }
        return false;
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    protected $fillable = [
        'description','user_id'
    ];

    /**
     * The Events that belong to the EventType.
     */
    public function events()
    {
        return $this->belongsToMany('App\Event');
    }

    public function is_common()
    {
        if(is_null($this->user_id)){
            return true;
        }
        return false;
    }
}

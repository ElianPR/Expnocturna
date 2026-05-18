<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CameraAnimation extends Model
{
    protected $fillable = [
        'title',
        'mp4_file',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class, 'camera_animation_event', 'camera_animation_id', 'event_id');
    }
}

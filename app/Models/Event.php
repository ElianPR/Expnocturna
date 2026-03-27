<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    protected $table = 'events';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'monogram',
        'typography',
        'template',
        'album',
        'song',
        'watermark',
        'date',
        'id_user',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // public function photos()
    // {
    //     return $this->hasMany(Photo::class, 'id_event');
    // }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = hex2bin(str_replace('-', '', Str::uuid()));
            }
        });
    }

    public function getIdHexAttribute()
    {
        return bin2hex($this->id);
    }
}
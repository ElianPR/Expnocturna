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
        'id',
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

    protected $casts = [
        'date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->id) {
                $model->id = hex2bin(str_replace('-', '', (string) Str::uuid()));
            }
        });
    }

    public function getIdHexAttribute(): string
    {
        return bin2hex($this->id);
    }
}
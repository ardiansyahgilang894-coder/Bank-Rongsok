<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityImage extends Model
{
    protected $fillable = [
        'activity_id',
        'image_path',
        'caption',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }


    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}

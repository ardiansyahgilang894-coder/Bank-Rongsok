<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Activity extends Model
{
    protected $fillable = [
        'title',
        'description',
        'date',
        'location',
        'status',
        'created_by',
    ];

    protected static function booted()
    {
        static::deleting(function ($activity) {

            foreach ($activity->images as $image) {

                if (
                    $image->image_path &&
                    Storage::disk('public')->exists($image->image_path)
                ) {
                    Storage::disk('public')
                        ->delete($image->image_path);
                }
            }

            // delete semua record sekaligus
            $activity->images()->delete();
        });
    }

    public function images()
    {
        return $this->hasMany(ActivityImage::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

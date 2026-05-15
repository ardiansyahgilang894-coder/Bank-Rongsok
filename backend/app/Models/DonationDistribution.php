<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonationDistribution extends Model
{
    protected $fillable = [
        'title',
        'description',
        'location',
        'distribution_date',
        'recipient_count',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'distribution_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(DistributionItem::class, 'distribution_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributionItem extends Model
{
    protected $fillable = [
        'distribution_id',
        'item_name',
        'quantity',
        'notes',
    ];

    public function distribution()
    {
        return $this->belongsTo(DonationDistribution::class, 'distribution_id');
    }
}

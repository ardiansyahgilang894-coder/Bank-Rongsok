<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScrapSale extends Model
{
    protected $fillable = [
        'sale_date',
        'item_name',
        'quantity',
        'weight_kg',
        'price_per_unit',
        'total_price',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:2',
        'price_per_unit' => 'decimal:2',
        'total_price' => 'decimal:2',
        'sale_date' => 'date',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

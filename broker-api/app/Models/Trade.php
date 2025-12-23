<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trade extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function buyingOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function sellingOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

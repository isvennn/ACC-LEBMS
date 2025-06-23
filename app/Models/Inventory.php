<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_number',
        'item_id',
        'beginning_inventory',
        'ending_inventory',
        'starting_period',
        'ending_period',
        'total_borrowed',
        'usable_quantity',
        'damaged_quantity',
        'lost_quantity',
        'repaired_qty',
        'disposed_quantity',
        'laboratory_id',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Laboratory::class);
    }
}

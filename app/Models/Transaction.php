<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_no',
        'item_id',
        'user_id',
        'reserve_quantity',
        'approve_quantity',
        'date_of_usage',
        'date_of_return',
        'time_of_return',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'date_of_usage' => 'date',
            'date_of_return' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function transactionStatuses(): HasMany
    {
        return $this->hasMany(TransactionStatus::class);
    }

    public function transactionPenalties(): HasMany
    {
        return $this->hasMany(TransactionPenalty::class);
    }
}

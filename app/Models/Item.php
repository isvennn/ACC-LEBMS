<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Item extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'item_name',
        'item_description',
        'item_price',
        'category_id',
        'beginning_qty',
        'current_qty'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('qrcode')->singleFile();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
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

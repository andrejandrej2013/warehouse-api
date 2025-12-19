<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot([
                'quantity',
                'price',
            ])
            ->withTimestamps();
    }

}

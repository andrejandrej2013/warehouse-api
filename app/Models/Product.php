<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'price',
        'quantity',
    ];

    protected $casts = [
        'price' => 'integer',
        'quantity' => 'integer',
    ];

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)
            ->withPivot([
                'quantity',
                'price',
            ])
            ->withTimestamps();
    }

}

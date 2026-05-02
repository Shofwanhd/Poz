<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Produk extends Model
{
    protected $fillable = [
        'category_id',
        'uuid',
        'name',
        'slug',
        'BasePrice',
        'SellPrice',
        'stok',
        'SKU',
        'image'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->slug = Str::slug($model->name);
            $model->uuid = (string) Str::uuid();
        });
    }

    public function category(): BelongsTo
    {
        return $this->BelongsTo(Category::class);
    }
}

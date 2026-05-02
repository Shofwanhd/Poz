<?php

namespace App\Models;

use App\Models\Produk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'slug',
    ];

    // auto generate slug
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->slug = Str::slug($model->name);
            $model->uuid = (string) Str::uuid();
        });
    }

    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class, 'category_id', 'id');
    }
}

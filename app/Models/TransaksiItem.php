<?php

namespace App\Models;

use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiItem extends Model
{
    protected $fillable = ['transaksi_id', 'produk_name', 'price', 'qty'];

    public function transaksi(): BelongsTo
    {
        return $this->BelongsTo(Transaksi::class);
    }
}

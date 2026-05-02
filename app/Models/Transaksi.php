<?php

namespace App\Models;

use App\Models\TransaksiItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Transaksi extends Model
{
    protected $fillable = ['discount_name', 'namaPelanggan', 'note', 'subtotal', 'discount', 'total', 'statusPayment', 'statusOrder', 'payment_method', 'paid_amount', 'change', 'cashier'];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();

            $today = now()->format('ymd');
            $last = self::whereDate('created_at', today())
                ->latest('id')
                ->first();
            if ($last) {
                $lastNumber = (int) substr($last->idTransaksi, -3);
                $next = $lastNumber + 1;
            } else {
                $next = 1;
            }
            $model->idTransaksi = 'TRX-' . $today . str_pad($next, 3, '0', STR_PAD_LEFT);
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransaksiItem::class);
    }
}

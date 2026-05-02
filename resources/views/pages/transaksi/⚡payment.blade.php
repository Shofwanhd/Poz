<?php

use Livewire\Component;
use App\Models\Discount;
use App\Models\PaymentMethod;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\Produk;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $cart = [];

    public $customer_name;
    public $discount_id;
    public $note;

    public $type = null; // payment method id
    public $amount;
    public $paidAmount = 0;

    public $transaksi_id = null;
    public $transaksi_uuid = null;
    public $paymentmethods = [];

    public $produkStok;

    // public function with()
    // {
    //     return [
    //         'paymentmethods' => PaymentMethod::all()
    //     ];

    //     dd($paymentmethods);
    // }

    public function mount($uuid = null)
    {
        $this->paymentmethods = PaymentMethod::all();

        if ($uuid) {
            // =========================
            // 🔥 MODE 1: OPEN TABLE (DB)
            // =========================
            $trx = Transaksi::with('items')->where('uuid', $uuid)->firstOrFail();

            $this->transaksi_id = $trx->id;
            $this->transaksi_uuid = $trx->uuid;

            $this->customer_name = $trx->namaPelanggan;
            $this->discount_id = $trx->discount_id;
            $this->note = $trx->note;

            // convert items → cart
            $this->cart = $trx->items
                ->map(function ($item) {
                    return [
                        'produk_id' => $item->produk_id,
                        'name' => $item->produk_name,
                        'price' => $item->price,
                        'qty' => $item->qty,
                    ];
                })
                ->values()
                ->toArray();
        } else {
            // =========================
            // 🔥 MODE 2: SESSION (CHECKOUT)
            // =========================
            $checkout = session('checkout', []);

            $this->cart = $checkout['cart'] ?? [];
            $this->customer_name = $checkout['customer_name'] ?? '';
            $this->discount_id = $checkout['discount_id'] ?? null;
            $this->note = $checkout['note'] ?? '';
        }
    }

    public function getIsCashProperty()
    {
        if (!$this->type) {
            return false;
        }
        $method = collect($this->paymentmethods)->firstWhere('name', $this->type);

        return str_contains(strtolower($method?->name), 'cash');
    }

    public function getDiscountNameProperty()
    {
        if (!$this->discount_id) {
            return null;
        }

        $discount = Discount::find($this->discount_id);

        return $discount?->name;
    }

    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['qty'];
        });
    }

    public function getDiscountValueProperty()
    {
        if (!$this->discount_id) {
            return 0;
        }

        $discount = Discount::find($this->discount_id);

        if (!$discount) {
            return 0;
        }

        if ($discount->type === 'percentage') {
            $calculated = ($this->subtotal * $discount->value) / 100;

            return $discount->max_discount != 0 ? min($calculated, $discount->max_discount) : $calculated;
        }
        // dd($discount_name);
        return $discount->value;
    }

    public function getTotalProperty()
    {
        return max(0, $this->subtotal - $this->discountValue);
    }

    public function getChangeProperty()
    {
        if (!$this->isCash) {
            return 0;
        }

        $bayar = (int) $this->amount;
        $tagihan = (int) $this->total;

        return max(0, $bayar - $tagihan);
    }

    public function pay()
    {
        // dd($this->cart);
        if ($this->isCash && $this->amount < $this->total) {
            $this->addError('amount', 'Uang tidak cukup');
            return;
        }

        if ($this->isCash) {
            $this->paidAmount = $this->amount;
        } else {
            $this->paidAmount = $this->subtotal;
        }

        $stok = Produk::whereIn('id', array_keys($this->cart))->pluck('stok', 'id');

        foreach ($this->cart as $id => $item) {
            $currentStock = $stok[$id] ?? null;

            // stok null = tak terbatas
            if (is_null($currentStock)) {
                continue;
            }

            if ($item['qty'] > $currentStock) {
                $this->addError('cart', $item['name'] . ' stok habis');
                return;
            }
        }

        DB::transaction(function () {
            if ($this->transaksi_id) {
                // =====================
                // UPDATE (open table)
                // =====================
                $trx = Transaksi::find($this->transaksi_id);
                // dd($trx);
                $trx->update([
                    'total' => $this->total,
                    'statusPayment' => 'paid',
                    'paid_amount' => $this->paidAmount,
                    'change' => $this->change,
                    'payment_method' => $this->type,
                ]);
            } else {
                // =====================
                // INSERT (session)
                // =====================
                $trx = Transaksi::create([
                    'namaPelanggan' => $this->customer_name,
                    'discount_name' => $this->discountName,
                    'note' => $this->note,
                    'subtotal' => $this->subtotal,
                    'discount' => $this->discountValue,
                    'total' => $this->total,
                    'statusPayment' => 'paid',
                    'statusOrder' => 'Process',
                    'payment_method' => $this->type,
                    'paid_amount' => $this->paidAmount,
                    'change' => $this->change,
                    'cashier' => Auth::user()->name,
                ]);

                foreach ($this->cart as $id => $item) {
                    TransaksiItem::create([
                        'transaksi_id' => $trx->id,
                        'produk_id' => $id,
                        'produk_name' => $item['name'],
                        'price' => $item['price'],
                        'qty' => $item['qty'],
                    ]);
                }

                foreach ($this->cart as $id => $item) {
                    Produk::where('id', $id)->decrement('stok', $item['qty']);
                }
            }

            $uuid = $trx->uuid;

            return redirect()->route('order.success', $uuid);
        });
    }
};
?>

<div>
    {{-- Smile, breathe, and go slowly. - Thich Nhat Hanh --}}
    @if ($this->transaksi_uuid != null)
        <x-button.back title="Payment" link="/transaksi/{{ $this->transaksi_uuid }}" />
    @else
        <x-button.back title="Payment" link="/checkout" />
    @endif


    @if ($this->total != null)
        <div pt-4 items-center>
            <flux:heading size="lg" class="text-center">Summary</flux:heading>

            <div class="flex justify-center">
                <flux:card class="space-y-6 max-w-2xl w-full mt-4">
                    <div class="grid grid-cols-1 gap-4 pt-4">
                        <flux:heading size="lg">Nama : {{ $customer_name }}</flux:heading>
                        <flux:heading size="lg">Discount : {{ $this->discountName }}</flux:heading>
                        <flux:heading size="lg">Note : {{ $note }}</flux:heading>
                    </div>
                    <flux:separator class="mt-4 mb-4" />
                    <flux:heading size="lg">Detail Pesanan : </flux:heading>
                    <div class="space-y-6">
                        @foreach ($cart as $item)
                            <div class="flex justify-between">
                                <div>{{ $item['name'] }}</div>
                                <div>{{ $item['qty'] }} x Rp {{ number_format($item['price']) }}</div>
                            </div>
                        @endforeach
                    </div>
                    <flux:separator class="mt-4 mb-4" />
                    <div>
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($this->subtotal) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span>Discount</span>
                            <span>- Rp {{ number_format($this->discountValue) }}</span>
                        </div>

                        <div class="flex justify-between font-bold text-lg">
                            <span>Total</span>
                            <span>Rp {{ number_format($this->total) }}</span>
                        </div>
                    </div>
                    <flux:separator class="mt-4 mb-4" />
                    {{-- <flux:heading size="lg">Metode Pembayaran : </flux:heading> --}}
                    <flux:radio.group label="Metode Pembayaran" wire:model.live="type">
                        @foreach ($paymentmethods as $paymentmethod)
                            <flux:radio value="{{ $paymentmethod->name }}" label="{{ $paymentmethod->name }}" />
                        @endforeach
                    </flux:radio.group>
                    <x-form.input type="number" label="Uang Bayar" placeholder="Amount" name="amount"
                        wire:model.live="amount" :disabled="!$this->isCash" />

                    @if ($this->isCash)
                        <div class="mt-2 text-lg font-bold">
                            Kembalian: Rp {{ number_format($this->change) }}
                        </div>
                    @endif
                    <div class="space-y-2">
                        <flux:button variant="primary" class="w-full" wire:click="pay">Bayar</flux:button>
                    </div>
                </flux:card>
            </div>

        </div>
    @else
        <div class="pt-4 items-center">
            <flux:card size="sm" class="dark:bg-zinc-700 h-full flex flex-col items-center">
                <flux:heading size="lg" class="text-center">Oopss keranjang anda masih kosong</flux:heading>
                <img src="{{ asset('/storage/assets/empty-cart.png') }}"
                    class="w-20 h-20 md:w-50 md:h-50 object-cover justify-center" />
                <flux:button><a href="/kasir">Pilih Item</a></flux:button>
            </flux:card>
        </div>
    @endif
</div>

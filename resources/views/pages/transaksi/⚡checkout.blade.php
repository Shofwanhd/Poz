<?php

use Livewire\Component;
use App\Models\Discount;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\Produk;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $cart = [];

    public $discount_id = null;
    public $discountValue = 0;

    public $customer_name;
    public $discount_name;
    public $note;

    public function mount()
    {
        $this->cart = session('cart', []);

        $checkout = session('checkout', []);

        $this->cart = $checkout['cart'] ?? session('cart', []);
        $this->customer_name = $checkout['customer_name'] ?? '';
        $this->discount_id = $checkout['discount_id'] ?? null;
        $this->note = $checkout['note'] ?? '';
    }

    public function with()
    {
        return [
            'discounts' => Discount::where('is_active', 1)->get(),
        ];
    }

    public function increaseQty($id)
    {
        if (isset($this->cart[$id])) {
            $this->cart[$id]['qty']++;
        }

        session()->put('cart', $this->cart);
    }

    public function decreaseQty($id)
    {
        if (isset($this->cart[$id])) {
            $this->cart[$id]['qty']--;

            if ($this->cart[$id]['qty'] <= 0) {
                unset($this->cart[$id]);
            }
        }

        session()->put('cart', $this->cart);
    }

    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['qty'];
        });
    }

    public function updatedDiscountId($value)
    {
        if (!$value) {
            $this->discountValue = 0;
            return;
        }

        $discount = Discount::find($value);

        if ($discount) {
            if ($discount->type === 'percentage') {
                if ($discount->max_discount != 0 && ($this->subtotal * $discount->value) / 100 > $discount->max_discount) {
                    $this->discountValue = $discount->max_discount;
                } else {
                    $this->discountValue = ($this->subtotal * $discount->value) / 100;
                }
            } else {
                $this->discountValue = $discount->value;
            }
        }
    }

    public function getTotalProperty()
    {
        return max(0, $this->subtotal - $this->discountValue);
    }

    public function getDiscountNameProperty()
    {
        if (!$this->discount_id) {
            return null;
        }

        $discount = Discount::find($this->discount_id);

        return $discount?->name;
    }

    public function goToPayment()
    {
        $this->validate([
            'customer_name' => 'required',
            'discount_id' => 'nullable',
            'note' => 'nullable',
        ]);

        session()->put('checkout', [
            'cart' => $this->cart,
            'customer_name' => $this->customer_name,
            'discount_id' => $this->discount_id,
            'note' => $this->note,
        ]);

        return redirect()->route('payment');
    }

    public function saveOrder()
    {
        $this->validate([
            'customer_name' => 'required',
            'discount_id' => 'nullable',
            'note' => 'nullable',
        ]);

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
        // dd('masuk');
        DB::transaction(function () {
            $trx = Transaksi::create([
                'namaPelanggan' => $this->customer_name,
                'discount_name' => $this->discountName,
                'note' => $this->note,
                'subtotal' => $this->subtotal,
                'discount' => $this->discountValue,
                'total' => $this->total,
                'statusPayment' => 'pending',
                'statusOrder' => 'Process',
                'payment_method' => null,
                'paid_amount' => null,
                'change' => null,
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

            session()->forget(['cart', 'checkout']);

            return redirect()->route('kasir');
        });
    }
};
?>

<div>
    <x-button.back title="Checkout" link="/kasir" />

    @if ($this->total != null)

        <div class="pt-4 items-center">
            <flux:card size="sm" class="dark:bg-zinc-700 h-full flex flex-col">
                <flux:heading size="lg" class="text-center">Detail Pemesanan</flux:heading>
                <form>
                    <div class="grid grid-cols-2 md:grid-cols-2 gap-4 pt-4">
                        <div>
                            <x-form.input type="text" label="Nama Pelanggan" placeholder="Iqbal Bayu"
                                wire:model="customer_name" required />
                        </div>
                        <div>
                            <flux:select label="Discount" wire:model.live="discount_id">
                                <flux:select.option value="">Pilih Discount</flux:select.option>
                                @foreach ($discounts as $discount)
                                    <flux:select.option value="{{ $discount->id }}">
                                        {{ $discount->name }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                    </div>
                    <div class="pt-2">
                        <flux:textarea wire:model="note" label="Catatan"
                            placeholder="No lettuce, tomato, or onion..." />
                    </div>

            </flux:card>
        </div>
        <flux:separator class="mt-4 mb-4" />
        <flux:heading size="lg" class="pb-4">Items : </flux:heading>
        @foreach ($cart as $id => $item)
            <div class="pb-4">
                <flux:card size="sm" class="dark:bg-zinc-700 h-full flex flex-col pt-4">
                    <div class="flex justify-between items-center py-2">
                        <div>
                            {{ $item['name'] }}
                            <div class="text-sm text-zinc-400">
                                Rp {{ number_format($item['price']) }}
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <flux:button variant="primary" color="red" wire:click="decreaseQty({{ $id }})">
                                -</flux:button>
                            <flux:text class="text-base" variant="strong">{{ $item['qty'] }}</flux:text>
                            <flux:button variant="primary" color="blue" wire:click="increaseQty({{ $id }})">
                                +</flux:button>
                        </div>
                    </div>
                </flux:card>
            </div>
        @endforeach
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
        <div class="grid grid-cols-2 md:grid-cols-2 gap-4 pt-4">
            <div>
                <flux:button class="w-full" variant="primary" wire:click="saveOrder">Save Order</flux:button>

            </div>
            <div class="w-full">
                <flux:button class="w-full" variant="primary" color="blue" wire:click="goToPayment">Placed Order
                </flux:button>
            </div>
            </form>
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

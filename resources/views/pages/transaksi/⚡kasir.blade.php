<?php

use Livewire\Component;
use App\Models\Produk;
use App\Models\Category;

new class extends Component {
    public $cart = [];
    public function with()
    {
        return [
            //'produks' => Produk::with('category')->where('stok', '>', 0)->orWhere('stok', null)->get(),
            'produks' => Produk::with('category')->get(),
        ];
    }

    public function mount()
    {
        $this->cart = session('cart', []);
    }

    public function resetCart()
    {
        session()->forget(['cart', 'checkout']);
        $this->cart = [];
    }

    public function addToCart($id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return;
        }

        if (isset($this->cart[$id])) {
            $this->cart[$id]['qty']++;
        } else {
            $this->cart[$id] = [
                'name' => $produk->name,
                'price' => $produk->SellPrice,
                'qty' => 1,
            ];
        }
        session()->put('cart', $this->cart);
        // dd($this->cart);
    }

    public function getTotalProperty()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['qty'];
        });
    }
};
?>

<div>
    <flux:heading size="xl" class="pb-4">Kasir</flux:heading>

    <div class="flex mt-4 justify-end">
        @if (count($cart) > 0)
            <flux:button wire:click="resetCart">
                Reset Cart
            </flux:button>
        @endif
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-5 md:grid-cols-6 gap-4 pt-4">

        @foreach ($produks as $produk)
            @if (is_null($produk->stok) || $produk->stok > 0)
                <a href="#" wire:click.prevent="addToCart({{ $produk->id }})" class="block">
            @endif
            <flux:card size="sm"
                class="hover:bg-zinc-50 dark:hover:bg-zinc-700 h-full flex flex-col {{ $produk->stok === 0 ? 'opacity-50 grayscale pointer-events-none' : '' }}">

                <!-- 🔥 IMAGE FIX -->
                <div class="w-full aspect-square overflow-hidden rounded-lg">
                    <img src="{{ $produk->image ? asset('/storage/produk/' . $produk->image) : asset('/storage/produk/notfound.png') }}"
                        class="w-full h-full object-cover" />
                </div>

                <!-- 🔥 CONTENT -->
                <div class="mt-3 flex flex-col flex-1">
                    <flux:heading size="lg" class="line-clamp-1">
                        {{ $produk->name }}
                    </flux:heading>

                    <flux:heading class="mt-1">
                        Rp {{ number_format($produk->SellPrice) }}
                    </flux:heading>

                    <div class="flex items-center justify-between pt-3">

                        <!-- kiri -->
                        <flux:badge color="green">
                            {{ $produk->category->name }}
                        </flux:badge>
                        <!-- kanan -->

                        @if ($produk->stok === 0)
                            <flux:badge color="red">
                                Kosong
                            </flux:badge>
                        @elseif($produk->stok === null)
                        @else
                            @if ($produk->stok <= 5)
                                <flux:badge color="red">
                                    {{ $produk->stok }}
                                </flux:badge>
                            @elseif ($produk->stok <= 10)
                                <flux:badge color="yellow">
                                    {{ $produk->stok }}
                                </flux:badge>
                            @else
                                <flux:badge color="blue">
                                    {{ $produk->stok }}
                                </flux:badge>
                            @endif
                        @endif

                    </div>

                </div>

            </flux:card>
            </a>
        @endforeach


    </div>
    @if (count($cart) > 0)
        <div class="sticky z-10 relative bottom-0 pt-4 pb-4 mx-auto">
            <a href="{{ route('checkout') }}" wire:navigate>
                <flux:card size="sm" class="bg-blue-400 hover:bg-blue-500 dark:hover:bg-blue-500 text-center">
                    <flux:heading size="lg" class="text-zinc-50 ">Checkout</flux:heading>
                    <flux:heading size="lg" class="text-zinc-50 ">
                        Rp {{ number_format($this->total) }}
                    </flux:heading>
                </flux:card>
            </a>
        </div>
    @endif
</div>

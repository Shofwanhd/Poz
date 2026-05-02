<?php

use Livewire\Component;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\GeneralTab;

new class extends Component {
    public $transaksiId;
    public $order;
    public $change;
    public $nama;
    public $date;
    public $idTransaksi;
    public $cashier;
    public $payment;
    public $subtotal;
    public $discount;
    public $paid;

    public function mount($uuid)
    {
        $order = Transaksi::where('uuid', $uuid)->firstOrFail();
        // dd($order->change);

        $this->transaksiId = $order->id;
        $this->change = $order->change;
        $this->nama = $order->namaPelanggan;
        $this->date = $order->created_at;
        $this->idTransaksi = $order->idTransaksi;
        $this->cashier = $order->cashier;
        $this->payment = $order->payment_method;
        $this->subtotal = $order->subtotal;
        $this->discount = $order->discount;
        $this->paid = $order->paid_amount;
    }

    public function with()
    {
        return [
            'gentab' => ($gentab = GeneralTab::first()),
            'items' => TransaksiItem::where('transaksi_id', $this->transaksiId)->get(),
        ];
    }

    public function toko()
    {
        return redirect()->route('kasir');
    }
};
?>

<div id="confetti-area" class="min-h-[100dvh]justify-between">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <!-- TOP SECTION -->
    <div class="pt-10 px-6 text-center">

        <!-- Circle check -->
        <div class="flex justify-center mt-10">
            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-lg">
                {{-- <flux:icon.check class="text-indigo-600 w-10 h-10" /> --}}
                <img src="{{ asset('/storage/assets/check.gif') }}" class="mx-auto" />
            </div>
        </div>

        <!-- Date -->
        <div class="mt-6 text-sm opacity-80">
            {{ $this->date->format('d M Y') }}
        </div>

        <!-- Title -->
        <div class="mt-2 text-2xl font-semibold">
            Transaction Successful!
        </div>

        <!-- Amount -->
        <div class="text-xl font-bold mt-2">
            Kembali Rp {{ number_format($change ?? 0, 0, ',', '.') }}
        </div>

        <!-- User Card -->
        <div class="mt-6 flex justify-center">
            <div class="grid grid-cols-2 md:grid-cols-2 gap-4 pt-4">
                <div>
                    <flux:button class="w-full" variant="primary"><a href="/kasir" wire:navigate>Kasir</a>
                    </flux:button>

                </div>
                <div class="w-full">
                    <flux:button class="w-full" variant="primary" color="blue" onclick="printStruk()">Print Struk
                    </flux:button>
                </div>
            </div>
        </div>
    </div>
    {{-- hidden print:block --}}
    <div id="print-area" class="hidden print:block flex justify-center pt-5 max-w-[280px]">
        <div>
            <div class="justify-center">
                <h2 class="font-bold text-center">{{ $gentab->NamaToko ?? 'Poz App' }}</h2>
                <p class="text-center">{{ $gentab->Alamat ?? 'by SHD' }}</p>
                <p>---------------------------------</p>
                <p class="font-bold text-center">{{ $idTransaksi }}</p>
            </div>
            <div class="grid grid-cols-[1fr_2fr] pt-4">
                <div>
                    <p>Tanggal</p>
                </div>
                <div class="col-span">
                    <p>: {{ $this->date->format('d M Y h:m') }}</p>
                </div>
                <div>
                    <p>Nama</p>
                </div>
                <div>
                    <p>: {{ $this->nama }}</p>
                </div>
                <div>
                    <p>Kasir</p>
                </div>
                <div>
                    <p>: {{ $this->cashier }}</p>
                </div>
                <div>
                    <p>Payment</p>
                </div>
                <div>
                    <p>: {{ $this->payment }}</p>
                </div>
            </div>
            <p>---------------------------------</p>
            <div class="grid grid-cols-[1.5fr_1fr] pt-4">
                @foreach ($items as $item)
                    <div>
                        <p>{{ $item->qty }} {{ $item->produk_name }}</p>
                    </div>
                    <div>
                        <p>Rp. {{ number_format($item->qty * $item->price ?? 0, 0, ',', '.') }}</p>
                    </div>
                @endforeach

            </div>
            <p>---------------------------------</p>

            <div class="grid grid-cols-[1.5fr_1fr] pt-4">
                <div>
                    <p class="font-bold">Subtotal</p>
                </div>
                <div>
                    <p>Rp. {{ number_format($subtotal ?? 0, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="font-bold">Discount</p>
                </div>
                <div>
                    <p>Rp. {{ number_format($discount ?? 0, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="font-bold">Total</p>
                </div>
                <div>
                    <p>Rp. {{ number_format($subtotal - $discount ?? 0, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="font-bold">{{ $payment }}</p>
                </div>
                <div>
                    <p>Rp. {{ number_format($paid ?? 0, 0, ',', '.') }}</p>
                </div>
                @if ($change != null)
                    <div>
                        <p class="font-bold">Kembali</p>
                    </div>
                    <div>
                        <p>Rp. {{ number_format($change ?? 0, 0, ',', '.') }}</p>
                    </div>
                @endif

            </div>
        </div>


    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #print-area,
            #print-area * {
                visibility: visible;
            }

            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>

    <script>
        function printStruk() {
            window.print();
        }
    </script>
    <script>
        const container = document.getElementById('confetti-area');
        document.addEventListener('DOMContentLoaded', function() {
            const duration = 3 * 1000;
            const end = Date.now() + duration;

            (function frame() {
                confetti({
                    particleCount: 5,
                    angle: 60,
                    spread: 55,
                    origin: {
                        x: 0
                    }
                });

                confetti({
                    particleCount: 5,
                    angle: 120,
                    spread: 55,
                    origin: {
                        x: 1
                    }
                });

                if (Date.now() < end) {
                    requestAnimationFrame(frame);
                }
            })();
        });
    </script>
</div>

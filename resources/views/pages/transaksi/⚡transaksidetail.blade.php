<?php

use Livewire\Component;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use Flux\Flux;

use App\Models\GeneralTab;

new class extends Component {
    public $transaksiId;
    public $transaksiUuid;
    public $idTransaksi;
    public $namaPelanggan;
    public $discountName;
    public $discount;
    public $note;
    public $statusPayment;
    public $statusOrder;
    public $paymentMethod;
    public $cashier;
    public $created_at;
    public $total;
    public $paid_amount;
    public $subtotal;
    public $change;

    public $details;

    public $gentab;

    public function mount($uuid)
    {
        $data = Transaksi::where('uuid', $uuid)->firstOrFail();

        $this->transaksiId = $data->id;
        $this->transaksiUuid = $data->uuid;
        $this->idTransaksi = $data->idTransaksi;
        $this->namaPelanggan = $data->namaPelanggan;
        $this->discountName = $data->discount_name;
        $this->discount = $data->discount;
        $this->note = $data->note;
        $this->statusPayment = $data->statusPayment;
        $this->statusOrder = $data->statusOrder;
        $this->paymentMethod = $data->payment_method;
        $this->cashier = $data->cashier;
        $this->created_at = $data->created_at;
        $this->total = $data->total;
        $this->paid_amount = $data->paid_amount;
        $this->subtotal = $data->subtotal;
        $this->change = $data->change;

        $this->details = TransaksiItem::where('transaksi_id', $data->id)->get();
        $this->gentab = GeneralTab::first();
        // dd($this->details);
    }

    public function changeStatusOrder($uuid)
    {
        $trx = Transaksi::where('uuid', $uuid)->firstOrFail();
        $trx->statusOrder = 'Done';
        $trx->save();
        // $this->loadData();
        Flux::toast('Order berhasil diperbaharui.');
        return redirect('/transaksi/' . $uuid);
    }

    public function payment($uuid)
    {
        return redirect('/payment/' . $uuid);
    }
};
?>

<div>
    {{-- Knowing is not enough; we must apply. Being willing is not enough; we must do. - Leonardo da Vinci --}}
    <x-button.back title="Detail Transaksi" link="/transaksi" />

    <div class="flex mt-4 justify-end">
        @if ($this->statusPayment == 'Paid')
            <flux:button class="mr-5" onclick="printStruk()">
                <flux:icon.printer />
            </flux:button>
        @endif
        @if ($this->statusOrder != 'Done')
            <flux:button class="mr-5" wire:click="changeStatusOrder('{{ $this->transaksiUuid }}')">
                <flux:icon.check-circle />
            </flux:button>
        @endif
        @if ($this->statusPayment != 'Paid')
            <flux:button variant="primary" color="blue" wire:click="payment('{{ $this->transaksiUuid }}')">
                Bayar</flux:button>
        @endif


    </div>

    <div pt-4 items-center>
        <div class="flex justify-center">
            <flux:card class="max-w-2xl w-full mt-4">
                <flux:heading size="lg">Detail Transaksi </flux:heading>

                <div class="flex justify-between items-center mt-4">
                    <div class="font-bold">{{ $this->idTransaksi }}</div>

                    <flux:text variant="strong">
                        {{ date('d/m/Y - H:i', strtotime($this->created_at)) }}
                    </flux:text>
                </div>
                <flux:separator class="mt-4 mb-4" />
                <div class="grid grid-cols-3 gap-4">

                    <div class="...">Nama</div>
                    <div class="col-span-2 ...">: {{ $this->namaPelanggan }}</div>
                    <div class="...">Discount :</div>
                    <div class="col-span-2 ...">: {{ $this->discountName }}</div>
                    <div class="...">Note </div>
                    <div class="col-span-2 ...">: {{ $this->note }}</div>
                    <div class="...">Payment </div>
                    <div class="col-span-2 ...">: {{ $this->paymentMethod }}</div>
                    <div class="...">Dibayar </div>
                    <div class="col-span-2 ...">: Rp {{ number_format($this->paid_amount, 0, ',', '.') }}</div>
                    <div class="...">Kembali </div>
                    <div class="col-span-2 ...">: Rp {{ number_format($this->change, 0, ',', '.') }}</div>
                    <div class="..."></div>
                </div>

                <div class="mt-3 flex justify-between text-sm">
                    <span>
                        @if ($this->statusOrder == 'Done')
                            <flux:badge color="green">{{ $this->statusOrder }}</flux:badge>
                        @else
                            <flux:badge color="red">{{ $this->statusOrder }}</flux:badge>
                        @endif
                        @if ($this->statusPayment == 'Paid')
                            <flux:badge color="green">{{ $this->statusPayment }}</flux:badge>
                        @else
                            <flux:badge color="red">{{ $this->statusPayment }}</flux:badge>
                        @endif
                    </span>
                    <div class="font-semibold">
                        Rp {{ number_format($this->total, 0, ',', '.') }}
                    </div>
                </div>

            </flux:card>

        </div>

        {{-- item --}}

        <div class="flex justify-center">
            <flux:card class="max-w-2xl w-full mt-4">
                <flux:heading size="lg">Items</flux:heading>

                <div class="mt-4 space-y-3">
                    @foreach ($details ?? [] as $detail)
                        <div class="flex justify-between items-center border-b pb-2">

                            <!-- Nama produk -->
                            <div class="font-medium">
                                {{ $detail->produk_name }}
                            </div>

                            <!-- Qty + harga -->
                            <div class="text-right text-sm text-gray-300">
                                x{{ $detail->qty }}
                                <span class="ml-2">
                                    Rp {{ number_format($detail->price, 0, ',', '.') }}
                                </span>
                            </div>

                        </div>
                    @endforeach
                </div>
            </flux:card>
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
                    <p>: {{ $this->created_at->format('d M Y h:m') }}</p>
                </div>
                <div>
                    <p>Nama</p>
                </div>
                <div>
                    <p>: {{ $this->namaPelanggan }}</p>
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
                    <p>: {{ $this->paymentMethod }}</p>
                </div>
            </div>
            <p>---------------------------------</p>
            <div class="grid grid-cols-[1.5fr_1fr] pt-4">
                @foreach ($details as $item)
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
                    <p class="font-bold">{{ $paymentMethod }}</p>
                </div>
                <div>
                    <p>Rp. {{ number_format($paid_amount ?? 0, 0, ',', '.') }}</p>
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
</div>

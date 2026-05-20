<?php

use Livewire\Component;
use App\Models\Transaksi;

new class extends Component {
    public $data;
    public $transaksis = [];
    public $sortBy = 'name';
    public $sortDirection = 'desc';

    public $sdate;
    public $edate;

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function mount()
    {
        // default: hari ini
        $this->transaksis = Transaksi::whereDate('created_at', today())->orderBy('created_at')->get();

        $this->sdate = today()->format('Y-m-d');
        $this->edate = today()->format('Y-m-d');
    }

    public function filter()
    {
        $this->transaksis = Transaksi::query()
            ->when(
                $this->sdate && $this->edate,
                function ($query) {
                    $query->whereBetween('created_at', [$this->sdate . ' 00:00:00', $this->edate . ' 23:59:59'])->orderBy('created_at');
                },
                function ($query) {
                    $query->whereDate('created_at', today())->orderBy('created_at');
                },
            )
            ->get();
    }

    // public function changeStatusOrder($uuid)
    // {
    //     $trx = Transaksi::where('uuid', $uuid)->firstOrFail();
    //     $trx->statusOrder = 'Done';
    //     $trx->save();
    //     // return [
    //     //     'transaksis' => Transaksi::whereDate('created_at', today())->get(),
    //     // ];
    // }
};
?>

<div>
    {{-- When there is no desire, all things are at peace. - Laozi --}}
    <flux:heading size="xl" class="pb-4">Transaksi </flux:heading>
    <div class="space-y-6 max-w-lg w-full mt-4 pb-10 items-center">
        <form wire:submit.prevent="filter">
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="..."> <x-form.input type="date" label="Start Date" placeholder="Start Date"
                            value="2005-01-18" name="sdate" wire:model="sdate" /></div>
                    <div class="..."> <x-form.input type="date" label="End Date" placeholder="End Date"
                            name="edate" wire:model="edate" /></div>
                    {{-- <div class="space-y-2 mt-6">
                        <flux:button variant="primary" class="w-full hidden md:block" type="submit">Tarik Report
                        </flux:button>
                    </div> --}}
                </div>
            </div>

            <div class="grid grid-cols-4 gap-4 space-y-2 mt-4">
                <flux:button variant="primary" class="w-full" type="submit">Filter Data</flux:button>
            </div>
        </form>
    </div>
    {{-- VIEW DESKTOP --}}
    <div class="hidden md:block">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>ID Transaksi</flux:table.column>
                <flux:table.column>Nama Pelanggan</flux:table.column>
                <flux:table.column>Total</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'max_discount'" :direction="$sortDirection"
                    wire:click="sort('max_discount')">Status Pesanan</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'is_active'" :direction="$sortDirection"
                    wire:click="sort('is_active')">Status Pembayaran</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'is_active'" :direction="$sortDirection"
                    wire:click="sort('is_active')">Tanggal Transaksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($transaksis as $transaksi)
                    <flux:table.row :key="$transaksi->id"
                        class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700 transition rounded-xl"
                        onclick="window.location='/transaksi/{{ $transaksi->uuid }}'">

                        <flux:table.cell>
                            {{ $transaksi->idTransaksi }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $transaksi->namaPelanggan }}
                        </flux:table.cell>
                        <flux:table.cell>
                            Rp. {{ number_format($transaksi->total) }}
                        </flux:table.cell>

                        <flux:table.cell>

                            @if ($transaksi->statusOrder == 'Done')
                                <flux:badge color="green">{{ $transaksi->statusOrder }}</flux:badge>
                            @else
                                <flux:badge color="red">{{ $transaksi->statusOrder }}</flux:badge>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell>
                            @if ($transaksi->statusPayment == 'Paid')
                                <flux:badge color="green">{{ $transaksi->statusPayment }}</flux:badge>
                            @else
                                <flux:badge color="red">{{ $transaksi->statusPayment }}</flux:badge>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ date('d/m/Y -  h:m', strtotime($transaksi->created_at)) }}
                        </flux:table.cell>

                        {{-- <flux:table.cell>
                            <div class="justify-center">

                                <flux:button class="mr-5" size="sm" onclick="event.stopPropagation();"
                                    wire:click="changeStatusOrder('{{ $transaksi->uuid }}')">
                                    <flux:icon.check-circle />
                                </flux:button>
                            </div>
                        </flux:table.cell> --}}

                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>

    {{-- VIEW MOBILE --}}
    <div class="block md:hidden space-y-4 pt-4">

        @foreach ($transaksis as $transaksi)
            <a href="/transaksi/{{ $transaksi->uuid }}">
                <flux:card class="p-4 hover:bg-zinc-50 dark:hover:bg-zinc-700 mt-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-sm text-zinc-500">
                                {{ date('d/m/Y - h:m', strtotime($transaksi->created_at)) }}
                            </div>
                            <div class="font-bold">
                                {{ $transaksi->idTransaksi }}
                            </div>

                            <div class="text-sm text-zinc-500">
                                {{ $transaksi->namaPelanggan }}
                            </div>
                        </div>
                        @if ($transaksi->statusPayment == 'Paid')
                            <flux:badge color="green">{{ $transaksi->statusPayment }}</flux:badge>
                        @else
                            <flux:badge color="red">{{ $transaksi->statusPayment }}</flux:badge>
                        @endif
                    </div>

                    <div class="mt-3 flex justify-between text-sm">
                        <span>{{ $transaksi->statusOrder }}</span>
                        <span class="font-semibold">
                            Rp {{ number_format($transaksi->total) }}
                        </span>
                    </div>


                </flux:card>
            </a>
        @endforeach

    </div>

</div>

<?php

use Livewire\Component;
use App\Models\Transaksi;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransaksiExport;

new class extends Component {
    public $sdate;
    public $edate;
    public function export()
    {
        $this->data = DB::table('transaksis')
            ->whereBetween('created_at', [Carbon::parse($this->sdate)->startOfDay(), Carbon::parse($this->edate)->endOfDay()])
            ->where('statusPayment', 'Paid')
            ->get();

        return Excel::download(new TransaksiExport($this->data), 'transaksi.xlsx');
    }
};
?>

<div>
    {{-- Nothing worth having comes easy. - Theodore Roosevelt --}}
    <x-button.back title="Report Transaksi" link="/report" />

    <div class="flex justify-center">
        <flux:card class="space-y-6 max-w-lg w-full mt-4 items-center">
            <form wire:submit="export">
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="..."> <x-form.input type="date" label="Start Date" placeholder="Start Date"
                                name="sdate" wire:model="sdate" /></div>
                        <div class="..."> <x-form.input type="date" label="End Date" placeholder="End Date"
                                name="edate" wire:model="edate" /></div>
                    </div>
                </div>

                <div class="space-y-2 mt-4">
                    <flux:button variant="primary" class="w-full" type="submit">Tarik Report</flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</div>

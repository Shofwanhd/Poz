<?php

use Livewire\Component;
use App\Models\PaymentMethod;
use App\Enums\LogAction;
use App\Models\SysLog;
use Carbon\Carbon;

new class extends Component
{
    public $name;

    public function store()
    {
        $this->validate([
            'name' => 'required',
        ]);

        $model = new PaymentMethod();

        $model->name = $this->name;

        $changes = [
            'name'=> $this->name,
        ];
        $model->save();

        SysLog::create([
            'user_id' => Auth::id(),
            'action' => LogAction::CREATE->value,
            'model' => class_basename($model),
            'model_id' => $model->id,
            'field' => 'name',
            'newValue' => $this->name,
            'actionDate' => Carbon::now(),
        ]);
        session()->flash('message', 'Data Post Berhasil Disimpan.');
        return redirect('/kelola-toko/metode-pembayaran');

    }
};
?>

<div>
    <x-button.back title="Tambah Metode Pembayaran" link="/kelola-toko/metode-pembayaran"/>

    <div class="flex justify-center">
        <flux:card class="space-y-6 max-w-lg w-full mt-4">
        <div>
            <flux:heading size="lg">Tambah Metode Pembayaran</flux:heading>
            <flux:text class="mt-2">Masukkan nama metode pembayaran</flux:text>
        </div>

        <form wire:submit="store">
        <div class="space-y-6">
            <x-form.input type="text" label="Nama Metode" placeholder="Nama Metode" wire:model="name"/>
        </div>

        <div class="space-y-2 mt-4">
            <flux:button variant="primary" class="w-full" type="submit">Simpan Metode Pembayaran</flux:button>
        </div>
        </form>
        </flux:card>
    </div>
</div>
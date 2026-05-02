<?php

use Livewire\Component;
use App\Models\Discount;
use App\Enums\LogAction;
use App\Models\SysLog;
use Flux\Flux;

new class extends Component
{
    public $type = 'amount';

    public $name;
    public $value;
    public $max_discount;
    public $is_active = 0;

    public function store()
    {
        $this->validate([
            'name' => 'required',
            'value' => 'required|numeric',
            'type' => 'required',
            // 'is_active' => 'required',
        ]);

        if ($this->type === 'amount') {
            $this->max_discount = null;
        }
        // dd($this->category);

        $model = new Discount();

        $model->name = $this->name;
        $model->type = $this->type;
        $model->value = $this->value;
        $model->max_discount = $this->max_discount;
        $model->is_active = $this->is_active;

        $changes = [
            'name'=> $this->name,
            'type' => $this->type,
            'value' => $this->value,
            'max_discount' => $this->max_discount,
            'is_active' => $this->is_active,
        ];
        $model->save();

        foreach ($changes as $field => $newValue) {
            SysLog::create([
                'user_id' => Auth::id(),
                'action' => LogAction::CREATE->value,
                'model' => class_basename($model),
                'model_id' => $model->id,
                'field' => $field,
                'oldValue' => null,
                'newValue' => $newValue,
                'actionDate' => now(),
            ]);
        }
        // $this->reset('category');
        Flux::toast('Discount Berhasil ditambahkan');
        $this->dispatch('toast', message: 'Data berhasil disimpan', type: 'success');
        session()->flash('message', 'Data Post Berhasil Disimpan.');
        return redirect('/kelola-produk/discount');
        // $this->dispatch('close-modal', name: 'TambahKategori');
    }
};
?>

<div>
    <x-button.back title="Tambah Diskon" link="/kelola-produk/discount"/>

    <div class="flex justify-center">
        <flux:card class="space-y-6 max-w-lg w-full mt-4">
        <div>
            <flux:heading size="lg">Tambah Data Diskon</flux:heading>
            <flux:text class="mt-2">Masukkan detail diskon</flux:text>
        </div>

        <form wire:submit="store">
        <div class="space-y-6">
            <x-form.input type="text" label="Nama Diskon" placeholder="Nama Diskon" name="name" wire:model="name"/>
            <x-form.input type="number" label="Amount Discount" placeholder="Amount Discount" name="value" wire:model="value" x-on:input="$event.target.value = formatNumber($event.target.value)"/>

            <flux:radio.group label="Pilih Tipe Diskon" wire:model.live="type">
                <flux:radio value="amount" label="Amount" />
                <flux:radio value="percentage" label="Percentage"/>
            </flux:radio.group>

            <x-form.input type="number" label="Max Discount" placeholder="Max Discount" name="max_discount" wire:model="max_discount" :disabled="$type !== 'percentage'"/>

            <flux:field variant="inline">
                <flux:label>Active</flux:label>
                <flux:switch wire:model.live="is_active" />
                <flux:error name="is_active" />
            </flux:field>

        </div>

        <div class="space-y-2 mt-4">
            <flux:button variant="primary" class="w-full" type="submit">Simpan Diskon</flux:button>
        </div>
        </form>
        </flux:card>
    </div>
</div>
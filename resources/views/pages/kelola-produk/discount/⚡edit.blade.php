<?php

use Livewire\Component;
use App\Models\Discount;
use App\Enums\LogAction;
use App\Models\SysLog;

new class extends Component
{
    public $discountId;

    public $name;
    public $value;
    public $type = 'amount';
    public $max_discount;
    public $is_active = true;

    public function mount($uuid)
    {
        $data = Discount::where('uuid', $uuid)->firstOrFail();

        $this->discountId = $data->id;
        $this->name = $data->name;
        $this->value = $data->value;
        $this->type = $data->type;
        $this->max_discount = $data->max_discount;
        $this->is_active = $data->is_active;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required',
            'value' => 'required|numeric',
            'type' => 'required',
            'max_discount' => $this->type === 'percentage'
                ? 'required|numeric'
                : 'nullable',
        ]);

        if ($this->type === 'amount') {
            $this->max_discount = null;
        }

        $model = Discount::findOrFail($this->discountId);
        $old = $model->getOriginal();

        $model->fill([
            'name' => $this->name,
            'type' => $this->type,
            'value' => $this->value,
            'max_discount' => $this->max_discount,
            'is_active' => $this->is_active,
        ]);

        $changes = $model->getDirty();

        Discount::where('id', $this->discountId)
            ->update([
                'name' => $this->name,
                'type' => $this->type,
                'value' => $this->value,
                'max_discount' => $this->max_discount,
                'is_active' => $this->is_active,
            ]);
        
        foreach ($changes as $field => $newValue) {
            SysLog::create([
                'user_id' => Auth::id(),
                'action' => LogAction::UPDATE->value,
                'model' => class_basename($model),
                'model_id' => $model->id,
                'field' => $field,
                'oldValue' => $old[$field] ?? null,
                'newValue' => $newValue,
                'actionDate' => now(),
            ]);
        }

        return $this->redirect('/kelola-produk/discount');
    }

};
?>

<div>
    <x-button.back title="Edit Diskon" link="/kelola-produk/discount"/>

    <div class="flex justify-center">
        <flux:card class="space-y-6 max-w-lg w-full mt-4">
        <div>
            <flux:heading size="lg">Tambah Data Diskon</flux:heading>
            <flux:text class="mt-2">Masukkan detail diskon</flux:text>
        </div>

        <form wire:submit="update">
        <div class="space-y-6">
            <x-form.input type="text" label="Nama Diskon" placeholder="Nama Diskon" name="name" wire:model="name"/>
            <x-form.input type="number" label="Amount Discount" placeholder="Amount Discount" name="value" wire:model="value"/>

            <flux:radio.group label="Pilih Tipe Diskon" wire:model.live="type">
                <flux:radio value="amount" label="Amount" />
                <flux:radio value="percentage" label="Percentage"/>
            </flux:radio.group>

            <x-form.input type="number" label="Max Discount" placeholder="Max Discount" name="max_discount" wire:model="max_discount"/>

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
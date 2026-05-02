<?php

use Livewire\Component;
use App\Models\PaymentMethod;
use App\Enums\LogAction;
use App\Models\SysLog;
use Carbon\Carbon;

new class extends Component
{
    public $name;
    public $oldName;
    public $PaymentMethodId;
    public $PaymentMethodUuid;

    public function mount($uuid)
    {
        $data = PaymentMethod::where('uuid', $uuid)->firstOrFail();

        $this->name = $data->name;
        $this->oldName = $data->name;
        $this->PaymentMethodId = $data->id;
        $this->PaymentMethodUuid = $data->uuid;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
        ]);

        $model = PaymentMethod::findOrFail($this->PaymentMethodId);
        $old = $model->getOriginal();

        $model->fill([
            'name' => $this->name
        ]);
        
        $changes = $model->getDirty();

        PaymentMethod::where('id', $this->PaymentMethodId)
            ->update([
                'name'=> $this->name,
            ]);

        foreach ($changes as $field => $newValue) {

            SysLog::create([
                'user_id' => Auth::id(),
                'action' => LogAction::UPDATE->value,
                'model' => class_basename($model),
                'model_id' => $model->id,
                'field' => $field, // 🔥 tambahin ini di table
                'oldValue' => $old[$field] ?? null,
                'newValue' => $newValue,

                'actionDate' => now(),
            ]);
        }

        return $this->redirect('/kelola-toko/metode-pembayaran');
    }
};
?>

<div>
    <x-button.back title="Edit Metode Pembayaran" link="/kelola-toko/metode-pembayaran"/>

    <div class="flex justify-center">
        <flux:card class="space-y-6 max-w-lg w-full mt-4">
        <div>
            <flux:heading size="lg">Edit Metode Pembayaran</flux:heading>
            <flux:text class="mt-2">Ubah nama metode pembayaran</flux:text>
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
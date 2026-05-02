<?php

use Livewire\Component;
use App\Models\GeneralTab;
use App\Enums\LogAction;
use App\Models\SysLog;
use Carbon\Carbon;

new class extends Component {
    public $name;
    public $alamat;
    public $description;
    public $storeId;

    public function mount()
    {
        $data = GeneralTab::first();

        if ($data) {
            // mode EDIT
            $this->storeId = $data->id;
            $this->name = $data->NamaToko;
            $this->alamat = $data->Alamat;
            $this->description = $data->Deskripsi;
        } else {
            // mode CREATE (kosong)
            $this->storeId = null;
            $this->name = '';
            $this->alamat = '';
            $this->description = '';
        }
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
        ]);

        // ambil atau buat (singleton)
        $model = GeneralTab::first();

        if (!$model) {
            // ======================
            // CREATE
            // ======================
            $model = new GeneralTab();

            $model->NamaToko = $this->name;
            $model->Alamat = $this->alamat;
            $model->Deskripsi = $this->description;

            $changes = [
                'NamaToko' => $this->name,
                'Alamat' => $this->alamat,
                'Deskripsi' => $this->description,
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
        } else {
            // ======================
            // UPDATE
            // ======================
            $old = $model->getOriginal();

            $model->NamaToko = $this->name;
            $model->Alamat = $this->alamat;
            $model->Deskripsi = $this->description;

            $changes = $model->getDirty();

            $model->save();

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
        }

        return $this->redirect('/kelola-toko/toko');
    }
};
?>

<div>
    <x-button.back title="Nama Toko" link="/kelola-toko" />

    <div class="flex justify-center">
        <flux:card class="space-y-6 max-w-lg w-full mt-4">
            <div>
                <flux:heading size="lg">Nama Toko</flux:heading>
                <flux:text class="mt-2">Atur nama toko disini</flux:text>
            </div>

            <form wire:submit="store">
                <div class="space-y-6">
                    <x-form.input type="text" label="Nama Toko" placeholder="Nama Toko" wire:model="name" />
                    <x-form.input type="text" label="Alamat Toko" placeholder="Alamat Toko" wire:model="alamat" />
                    <x-textarea label="Deskripsi Toko" placeholder="No lettuce, tomato, or onion..."
                        wire:model="description" />
                </div>

                <div class="space-y-2 mt-4">
                    <flux:button variant="primary" class="w-full" type="submit">Simpan Nama Toko</flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</div>

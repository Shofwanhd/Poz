<?php

use Livewire\Component;
use App\Models\Category;
use App\Enums\LogAction;
use App\Models\SysLog;

new class extends Component
{
    public $category;

        public function store()
    {
        $this->validate([
            'category' => 'required',
        ]);
        
        $model = new Category();
        $model->name = $this->category;

        $changes = [
            'name'=> $this->category,
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

        session()->flash('message', 'Data Post Berhasil Disimpan.');
        return redirect('/kelola-produk/kategori');

    }
};
?>

<div>
    <x-button.back title="Tambah Kategori" link="/kelola-produk/kategori"/>

    <div class="flex justify-center">
        <flux:card class="space-y-6 max-w-lg w-full mt-4">
        <div>
            <flux:heading size="lg">Tambah Data Kategori</flux:heading>
            <flux:text class="mt-2">Masukkan nama kategori</flux:text>
        </div>

        <form wire:submit="store">
        <div class="space-y-6">
            <x-form.input type="text" label="Nama Kategori" placeholder="Nama Kategori" wire:model="category"/>
        </div>

        <div class="space-y-2 mt-4">
            <flux:button variant="primary" class="w-full" type="submit">Simpan Kategori</flux:button>
        </div>
        </form>
        </flux:card>
    </div>

</div>
<?php

use Livewire\Component;
use App\Models\Category;
use App\Enums\LogAction;
use App\Models\SysLog;

new class extends Component
{
    public $category;
    public $categoryId;

    public function mount($uuid)
    {
        $data = Category::where('uuid', $uuid)->firstOrFail();

        $this->category = $data->name;
        $this->categoryId = $data->id;
    }

    public function store()
    {
        $this->validate([
            'category' => 'required',
        ]);

        $model = Category::findOrFail($this->categoryId);
        $old = $model->getOriginal();
        $model->fill([
            'name' => $this->category,
        ]);
        
        $changes = $model->getDirty();

        Category::where('id', $this->categoryId)
            ->update([
                'name'=> $this->category,
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

        return $this->redirect('/kelola-produk/kategori');
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
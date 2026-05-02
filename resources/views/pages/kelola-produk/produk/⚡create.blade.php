<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Category;
use App\Models\Produk;
use App\Enums\LogAction;
use App\Models\SysLog;

new class extends Component
{
    use WithFileUploads;

    public $is_active = false;
    public $name;
    public $category_id;
    public $BasePrice;
    public $SellPrice;
    public $stok;
    public $SKU;
    public $image;

    public function with()
    {
        return [
            'categories' => Category::all()
        ];
    }

    public function store()
    {
        // dd($this->all());

        $this->validate([
            'name' => 'required',
            'category_id' => 'required|numeric',
            'BasePrice' => 'required|numeric',
            'SellPrice' => 'required|numeric',
            'stok' => 'nullable',
            'SKU' => 'nullable',
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png,webp',
            // 'is_active' => 'required',
        ]);
        
        if($this->image){
            $this->image->storeAs('produk', $this->image->hashName(), 'public');
            $imageName = $this->image->hashName();
        } else {
            $imageName = $model->image ?? null;
        }

        if ($this->is_active) {
            $this->stok = null;
        }

        $model = new Produk();

        $model->name = $this->name;
        $model->category_id = $this->category_id;
        $model->BasePrice = $this->BasePrice;
        $model->SellPrice = $this->SellPrice;
        $model->stok = $this->stok;
        $model->SKU = $this->SKU;
        $model->image = $imageName;

        $changes = [
            'name'=> $this->name,
            'category_id' => $this->category_id,
            'BasePrice' => $this->BasePrice,
            'SellPrice' => $this->SellPrice,
            'stok' => $this->stok,
            'SKU' => $this->SKU,
            'image' => $imageName,
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

        return redirect('/kelola-produk/produk');

    }
};
?>

<div>
    {{-- Breathing in, I calm body and mind. Breathing out, I smile. - Thich Nhat Hanh --}}
    <x-button.back title="Tambah Produk" link="/kelola-produk/produk"/>

    <div class="flex justify-center">
        <flux:card class="space-y-6 max-w-lg w-full mt-4">
        <div>
            <flux:heading size="lg">Tambah Data Produk</flux:heading>
            <flux:text class="mt-2">Masukkan detail Produk</flux:text>
        </div>

        <form wire:submit="store">
        <div class="space-y-6">
            <x-form.input type="text" label="Nama Produk" placeholder="Nama Produk" wire:model="name" required/>
            <flux:select label="Category" wire:model.live="category_id" required>
                <flux:select.option value="">Pilih Kategori</flux:select.option>
                @foreach ($categories as $category)
                    <flux:select.option value="{{ $category->id }}" wire:key="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                @endforeach
            </flux:select>
            <x-form.input type="number" label="Harga Dasar" placeholder="Harga Dasar" wire:model="BasePrice" required/>
            <x-form.input type="number" label="Harga Jual" placeholder="Harga Jual" wire:model="SellPrice" required/>
            <flux:field variant="inline">
                <flux:label>Stok tak terbatas</flux:label>
                <flux:switch wire:model.live="is_active" />
                <flux:error name="is_active" />
            </flux:field>
            <x-form.input type="number" label="Stok" placeholder="Stok" wire:model="stok" :disabled="$is_active" required/>
            <x-form.input type="text" label="SKU" placeholder="ABC-123" wire:model="SKU"/>
            <x-form.input type="file" label="Foto produk" placeholder="image" wire:model="image" accept=".png, .jpg, .jpeg, .webp"/>
            <flux:text class="mt-2" color="red">Format : jpg,jpeg,png,webp. Max size 2MB</flux:text>

        </div>

        <div class="space-y-2 mt-6">
            <flux:button variant="primary" class="w-full" type="submit">Simpan Kategori</flux:button>
        </div>
        </form>
        </flux:card>
    </div>    
</div>
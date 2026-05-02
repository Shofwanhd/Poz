<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Produk;
use App\Models\Category;
use App\Enums\LogAction;
use App\Models\SysLog;

new class extends Component
{
    use WithFileUploads;

    public $categories;
    public $produkId;

    public $is_active;
    
    public $name;
    public $category_id;
    public $BasePrice;
    public $SellPrice;
    public $stok;
    public $SKU;
    public $image;

    public $oldImage;    // untuk gambar lama


    public function mount($uuid)
    {
        $data = Produk::where('uuid', $uuid)->firstOrFail();

        $this->produkId = $data->id;
        $this->name = $data->name;
        $this->category_id = $data->category_id;
        $this->BasePrice = $data->BasePrice;
        $this->SellPrice = $data->SellPrice;
        $this->stok = $data->stok;
        $this->oldImage = $data->image;
        $this->image = null; // 🔥 penting

        if($data->stok === null){
            $this->is_active = true;
        }else{
            $this->is_active = false;
        }
    }
    

    
    public function with()
    {
        return [
            'categories' => Category::all()
        ];
    }

    public function update()
    {

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

    if ($this->is_active) {
        $this->stok = null;
    }

    $imageName = $this->oldImage;

    if ($this->image) {

        if ($this->oldImage) {
            Storage::disk('public')->delete('produk/' . $this->oldImage);
        }

        $this->image->store('produk', 'public');
        $imageName = $this->image->hashName();
    }

        $model = Produk::findOrFail($this->produkId);
        $old = $model->getOriginal();
        
        $model->fill ([
            'name'=> $this->name,
            'category_id' => $this->category_id,
            'BasePrice' => $this->BasePrice,
            'SellPrice' => $this->SellPrice,
            'stok' => $this->stok,
            'SKU' => $this->SKU,
            'image' => $imageName,
        ]);

        $changes = $model->getDirty();

        Produk::where('id', $this->produkId)
            ->update([
                'name'=> $this->name,
                'category_id' => $this->category_id,
                'BasePrice' => $this->BasePrice,
                'SellPrice' => $this->SellPrice,
                'stok' => $this->stok,
                'SKU' => $this->SKU,
                'image' => $imageName,
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

        return $this->redirect('/kelola-produk/produk');
    }
};
?>

<div>
    <x-button.back title="Edit Produk" link="/kelola-produk/produk"/>

<div class="flex justify-center">
    <flux:card class="space-y-6 max-w-lg w-full mt-4 text-center">

        <div class="flex justify-center">
            @if ($oldImage)
                <img src="{{ asset('storage/produk/' . $oldImage) }}" class="w-32 h-32 object-cover rounded-lg" />
            @else
                <img src="{{ asset('/storage/produk/notfound.png') }}" class="w-32 h-32 object-cover rounded-lg" />
            @endif
        </div>

        <div>
            <flux:heading size="lg" class="pt-4 text-center">
                {{ $this->name }}
            </flux:heading>

            {{-- <flux:text class="mt-2 text-center">
                Masukkan detail Produk
            </flux:text> --}}

            <!-- 🔥 Grid 2 kolom -->
            <div class="grid grid-cols-2 gap-4 mt-4">

                <flux:card size="sm" class="text-center">
                    <flux:heading>Harga Dasar</flux:heading>
                    <flux:heading size="lg">Rp. {{ number_format($this->BasePrice) }}</flux:heading>
                </flux:card>

                <flux:card size="sm" class="text-center">
                    <flux:heading>Harga Jual</flux:heading>
                    <flux:heading size="lg">Rp. {{ number_format($this->SellPrice) }}</flux:heading>
                </flux:card>

            </div>

            <!-- 🔥 Full width -->
            <flux:card size="sm" class="mt-4 text-center">
                <flux:heading>Keuntungan</flux:heading>
                @if ($this->SellPrice - $this->BasePrice > 0)
                    <flux:heading size="lg" color="green" class="text-green-600">
                        Rp. +{{ number_format($this->SellPrice - $this->BasePrice) }}
                    </flux:heading>
                @else
                    <flux:heading size="lg" color="green" class="text-red-600">
                        Rp. {{ number_format($this->SellPrice - $this->BasePrice) }}
                    </flux:heading>
                @endif

            </flux:card>
        </div>

    </flux:card>
</div>

    <div class="flex justify-center">
        <flux:card class="space-y-6 max-w-lg w-full mt-4">
        <div>
            <flux:heading size="lg">Tambah Data Produk</flux:heading>
            <flux:text class="mt-2">Masukkan detail Produk</flux:text>
        </div>

        <form wire:submit="update">
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
            <h2>Foto Produk : </h2>
            @if ($image)
                <img src="{{ $image->temporaryUrl() }}">
            @elseif ($oldImage)
                <img src="{{ asset('storage/produk/' . $oldImage) }}">
            @endif
            <flux:text class="mt-2" color="red">Format : jpg,jpeg,png,webp. Max size 2MB</flux:text>

        </div>

        <div class="space-y-2 mt-6">
            <flux:button variant="primary" class="w-full" type="submit">Simpan Kategori</flux:button>
        </div>
        </form>
        </flux:card>
    </div> 
    
</div>
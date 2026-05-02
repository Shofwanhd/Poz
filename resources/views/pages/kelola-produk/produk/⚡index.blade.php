<?php

use Livewire\Component;
use App\Models\Produk;
// use App\Models\Category;
use App\Enums\LogAction;
use App\Models\SysLog;

new class extends Component {
    public $deleteId;
    public $deleteName;

    public $sortBy = 'name';
    public $sortDirection = 'desc';
    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function with()
    {
        return [
            'produks' => Produk::with('category')->orderBy($this->sortBy, $this->sortDirection)->get(),
        ];
    }

    public function confirmDelete($id)
    {
        $data = Produk::findOrFail($id);

        $this->deleteId = $data->id;
        $this->deleteName = $data->name;
    }

    public function delete()
    {
        // dd($this->deleteId);
        $model = Produk::findOrFail($this->deleteId);
        $model->delete();

        SysLog::create([
            'user_id' => Auth::id(),
            'action' => LogAction::DELETE->value,
            'model' => class_basename($model),
            'model_id' => $model->id,
            'field' => 'name',
            'oldValue' => $this->deleteName,
            'actionDate' => now(),
        ]);
        $this->dispatch('category-deleted');
    }
};
?>

<div>
    {{-- Simplicity is the essence of happiness. - Cedric Bledsoe --}}
    <x-button.back title="Daftar Produk" link="/kelola-produk" />

    <div class="flex mt-4 justify-end">

        <flux:button>
            <a href="/kelola-produk/produk/create" wire:navigate>
                Tambah Produk
            </a>
        </flux:button>
    </div>


    <flux:table container:class="mt-4">
        <flux:table.columns class="">
            <flux:table.column>Foto Produk</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                wire:click="sort('name')">Nama Produk</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                wire:click="sort('name')">Kategori Produk</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                wire:click="sort('name')">Harga Jual</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                wire:click="sort('name')">Stok</flux:table.column>
            <flux:table.column class="justify-end"> </flux:table.column>
        </flux:table.columns>

        @foreach ($produks as $produk)
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell>
                        @if ($produk->image)
                            <flux:avatar size="xl" src="{{ asset('/storage/produk/' . $produk->image) }}" />
                        @else
                            <flux:avatar size="xl" src="{{ asset('/storage/produk/notfound.png') }}" />
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>{{ $produk->name }}</flux:table.cell>
                    <flux:table.cell>{{ $produk->category->name }}</flux:table.cell>
                    <flux:table.cell>Rp. {{ number_format($produk->SellPrice) }}</flux:table.cell>
                    <flux:table.cell>{{ $produk->stok }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex justify-end gap-2">
                            <flux:modal.trigger>
                                <a href="/kelola-produk/produk/edit/{{ $produk->uuid }}">
                                    <flux:button>
                                        <flux:icon.pencil-square />
                                    </flux:button>
                                </a>
                            </flux:modal.trigger>

                            <flux:modal.trigger name="delete-profile">
                                {{-- <flux:button variant="danger"><flux:icon.trash /></flux:button> --}}
                                <flux:button variant="danger" wire:click="confirmDelete({{ $produk->id }})">
                                    <flux:icon.trash />
                                </flux:button>
                            </flux:modal.trigger>
                        </div>

                    </flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        @endforeach
    </flux:table>

    <flux:modal name="delete-profile" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Kategori?</flux:heading>

                <flux:text class="mt-2">
                    Data yang sudah dihapus tidak dapat dikembalikan
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="danger" wire:click="delete"
                    x-on:click="$flux.modal('delete-profile').close()">Delete</flux:button>
            </div>
        </div>
    </flux:modal>
</div>

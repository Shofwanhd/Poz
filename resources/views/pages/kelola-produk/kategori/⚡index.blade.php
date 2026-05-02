<?php

use Livewire\Component;
use App\Models\Category;
use App\Enums\LogAction;
use App\Models\SysLog;

new class extends Component
{
    public $deleteId;
    public $deleteName;

    public $sortBy = 'name';
    public $sortDirection = 'desc';
    public function sort($column) {
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
            'categories' => Category::orderBy($this->sortBy, $this->sortDirection)->get()
        ];
    }

    public function confirmDelete($id)
    {
        $data = Category::findOrFail($id);

        $this->deleteId = $data->id;
        $this->deleteName = $data->name;
    }

    public function delete()
    {
        // dd($this->deleteId);
        $model = Category::findOrFail($this->deleteId);
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
    <x-button.back title="Kategori Produk" link="/kelola-produk"/>

    <div class="flex mt-4 justify-end">
    <flux:modal.trigger>
    <flux:button wire:key="resetForm">            
        <a href="/kelola-produk/kategori/create" wire:navigate>
                Tambah Kategori
        </a>
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="TambahKategori" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Tambah Kategori</flux:heading>
                <flux:text class="mt-2">Masukan nama kategori</flux:text>
            </div>
            <form wire:submit="store" x-on:submit="$flux.modal('TambahKategori').close()">
                <x-form.input type="text" label="Nama Kategori" placeholder="Nama Kategori" name="name" wire:model="category"/>
                <div class="flex mt-4">
                    <flux:spacer />
                    <flux:button type="submit" variant="primary" pt-4>Simpan Kategori</flux:button>
                </div>
            </form>

        </div>
    </flux:modal>
    </div>



    <!-- Set the height of the table container... -->
    <flux:table container:class="mt-4">
        <flux:table.columns sticky class="">
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Kategori</flux:table.column>
            <flux:table.column class="justify-end"> </flux:table.column>
        </flux:table.columns>

        @foreach ($categories as $category)
        <flux:table.rows>
            <flux:table.row>
                <flux:table.cell>{{ $category->name }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex justify-end gap-2">
                        <flux:modal.trigger>
                        <a href="/kelola-produk/kategori/edit/{{ $category->uuid }}">
                                <flux:button>
                                    <flux:icon.pencil-square/>
                                </flux:button>
                            </a>
                        </flux:modal.trigger>

                        <flux:modal.trigger name="delete-profile">
                            {{-- <flux:button variant="danger"><flux:icon.trash /></flux:button> --}}
                            <flux:button variant="danger" wire:click="confirmDelete({{ $category->id }})"><flux:icon.trash /></flux:button>
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

                <flux:button type="submit" variant="danger" wire:click="delete" x-on:click="$flux.modal('delete-profile').close()">Delete</flux:button>
            </div>
        </div>
        </flux:modal>


</div>

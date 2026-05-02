<?php

use Livewire\Component;
use App\Models\PaymentMethod;
use App\Enums\LogAction;
use App\Models\SysLog;
use Carbon\Carbon;

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
            'methods' => PaymentMethod::orderBy($this->sortBy, $this->sortDirection)->get()
        ];
    }

    public function confirmDelete($id)
    {
        $data = PaymentMethod::findOrFail($id);

        $this->deleteId = $data->id;
        $this->deleteName = $data->name;
    }

    public function delete()
    {
        // dd($this->deleteId);

        $model = PaymentMethod::findOrFail($this->deleteId);
        $model->delete();

        SysLog::create([
            'user_id' => Auth::id(),
            'action' => LogAction::DELETE->value,
            'model' => class_basename($model),
            'model_id' => $model->id,
            'field' => 'name',
            'oldValue' => $this->deleteName,
            'actionDate' => Carbon::now(),
        ]);

        $this->dispatch('category-deleted');
    }

};
?>

<div>
    <x-button.back title="Metode Pembayaran" link="/kelola-toko"/>

    <div class="flex mt-4 justify-end">
        <flux:button>
            <a href="/kelola-toko/metode-pembayaran/create" wire:navigate>
                Tambah Metode Pembayaran
            </a>    
        </flux:button>
    </div>

    <flux:table container:class="max-h-80 mt-4">
        <flux:table.columns sticky class="">
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Metode Pembayaran</flux:table.column>
            <flux:table.column class="justify-end"> </flux:table.column>
        </flux:table.columns>

        @foreach ($methods as $method)
        <flux:table.rows>
            <flux:table.row>
                <flux:table.cell>{{ $method->name }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex justify-end gap-2">
                        <flux:modal.trigger>
                        <a href="/kelola-toko/metode-pembayaran/edit/{{ $method->uuid }}">
                                <flux:button>
                                    <flux:icon.pencil-square/>
                                </flux:button>
                            </a>
                        </flux:modal.trigger>

                        <flux:modal.trigger name="delete-metode">
                            {{-- <flux:button variant="danger"><flux:icon.trash /></flux:button> --}}
                            <flux:button variant="danger" wire:click="confirmDelete({{ $method->id }})"><flux:icon.trash /></flux:button>
                        </flux:modal.trigger>
                        </div>

                    </flux:table.cell>
            </flux:table.row>
        </flux:table.rows>

        @endforeach
    </flux:table>

        <flux:modal name="delete-metode" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Metode Pembayaran?</flux:heading>

                <flux:text class="mt-2">
                    Data yang sudah dihapus tidak dapat dikembalikan
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="danger" wire:click="delete" x-on:click="$flux.modal('delete-metode').close()">Delete</flux:button>
            </div>
        </div>
        </flux:modal>

</div>
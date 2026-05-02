<?php

use Livewire\Component;
use App\Models\Discount;
use App\Enums\LogAction;
use App\Models\SysLog;

new class extends Component {
    public $name;
    public $deleteId;

    public $discount;
    public $discountId;

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

    // function retrive
    public function with()
    {
        return [
            'discounts' => Discount::orderBy($this->sortBy, $this->sortDirection)->get(),
        ];
    }

    public function confirmDelete($id)
    {
        $data = Discount::findOrFail($id);

        $this->deleteId = $data->id;
        $this->name = $data->name;
    }

    public function delete()
    {
        $model = Discount::findOrFail($this->deleteId);
        $model->delete();

        SysLog::create([
            'user_id' => Auth::id(),
            'action' => LogAction::DELETE->value,
            'model' => class_basename($model),
            'model_id' => $model->id,
            'field' => 'name',
            'oldValue' => $this->name,
            'actionDate' => now(),
        ]);

        $this->dispatch('category-deleted');
    }

    public function edit($id)
    {
        // $this->reset(['category', 'categoryId']);

        $data = Discount::findOrFail($id);

        $this->discountId = $data->id;
        $this->discount = $data->name;
    }
};
?>

<div>
    <x-button.back title="Diskon Produk" link="/kelola-produk" />

    <div class="flex mt-4 justify-end">

        <flux:button>
            <a href="/kelola-produk/discount/create" wire:navigate>
                Tambah Diskon
            </a>
        </flux:button>
    </div>

    <flux:toast x-on:toast.window="$flux.toast($event.detail.message, { type: $event.detail.type })" />

    {{-- @if (session()->has('message'))
        <flux:toast position="top end" />
    @endif --}}

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Nama</flux:table.column>
            <flux:table.column>Diskon</flux:table.column>
            {{-- <flux:table.column sortable :sorted="$sortBy === 'date'" :direction="$sortDirection" wire:click="sort('date')">Type</flux:table.column> --}}
            <flux:table.column sortable :sorted="$sortBy === 'max_discount'" :direction="$sortDirection"
                wire:click="sort('max_discount')">Max Discount</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'is_active'" :direction="$sortDirection"
                wire:click="sort('is_active')">Status</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($discounts as $discount)
                <flux:table.row :key="$discount->id">
                    <flux:table.cell class="whitespace-nowrap">{{ $discount->name }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">
                        @if ($discount->type == 'amount')
                            Rp. {{ number_format($discount->value) }}
                        @else
                            {{ number_format($discount->value) }} %
                        @endif
                    </flux:table.cell>

                    {{-- <flux:table.cell variant="strong">{{ $discount->type }}</flux:table.cell> --}}
                    <flux:table.cell class="whitespace-nowrap"> Rp.{{ number_format($discount->max_discount) }}
                    </flux:table.cell>

                    <flux:table.cell variant="strong">
                        @if ($discount->is_active == 1)
                            <flux:badge color="green">Active</flux:badge>
                        @elseif ($discount->is_active == 0)
                            <flux:badge color="red">Not Active</flux:badge>
                        @endif
                    </flux:table.cell>


                    <flux:table.cell>
                        <div class="flex justify-end gap-2">
                            <flux:modal.trigger name="edit-kategori">
                                <a href="/kelola-produk/discount/edit/{{ $discount->uuid }}">
                                    <flux:button>
                                        <flux:icon.pencil-square />
                                    </flux:button>
                                </a>
                            </flux:modal.trigger>

                            <flux:modal.trigger name="delete-discount">
                                <flux:button variant="danger" wire:click="confirmDelete({{ $discount->id }})">
                                    <flux:icon.trash />
                                </flux:button>
                            </flux:modal.trigger>
                        </div>

                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:modal name="delete-discount" class="min-w-[22rem]">
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
                    x-on:click="$flux.modal('delete-discount').close()">Delete</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Livewire component example code...
    use \Livewire\WithPagination;

    public $sortBy = 'date';
    public $sortDirection = 'desc';

    public function sort($column) {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[\Livewire\Attributes\Computed]
    public function orders()
    {
        return \App\Models\Order::query()
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate(5);
    }
-->
</div>

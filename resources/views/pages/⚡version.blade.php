<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div>
    <flux:heading size="xl" class="pb-4">Version History</flux:heading>

    <div class="container">
        <flux:card size="sm" class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
            <flux:heading class="flex items-center gap-2">1.0.0 Beta
            </flux:heading>
            <flux:text class="mt-1">2 May 2026</flux:text>
            <flux:heading class="mt-4">Whats New?</flux:heading>
            <flux:text class="mt-4">New</flux:text>
            <flux:text class="mt-2">
                <ul class="list-disc ml-4">
                    <li>Inital build</li>
                    <li>Add Kasir</li>
                    <li>Add Transaksi</li>
                    <li>Add Report</li>
                    <li>Add Kelola Produk</li>
                    <li>Add Kelola Toko</li>
                    <!-- ... -->
                </ul>
            </flux:text>

        </flux:card>
    </div>
</div>

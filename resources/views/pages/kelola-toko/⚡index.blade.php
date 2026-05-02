<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    <h1>Kelola Toko</h1>

    <div class="grid grid-cols-1 md:grid-cols-1 gap-3 mt-5">
        <div>
            <x-card.link 
            title="Toko" 
            link="/kelola-toko/toko" 
            description="Atur Nama Toko"/>
        </div>
        <div>
            <x-card.link 
            title="Metode Pembayaran" 
            link="/kelola-toko/metode-pembayaran" 
            description="Atur Metode Pembayaran"/>
        </div>
    </div>
</div>
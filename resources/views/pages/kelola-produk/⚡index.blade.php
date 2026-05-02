<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    <h1>Kelola Produk</h1>

    <div class="grid grid-cols-1 md:grid-cols-1 gap-3 mt-5">
        <div>
            <x-card.link 
            title="Daftar Produk" 
            link="/kelola-produk/produk" 
            description="Tambah atau Ubah Produk"/>
        </div>
        <div>
            <x-card.link 
            title="Kategori Produk" 
            link="/kelola-produk/kategori" 
            description="Atur Kategori Produk"/>
        </div>
        <div>
            <x-card.link 
            title="Diskon" 
            link="/kelola-produk/discount" 
            description="Atur Diskon Disini"/>
        </div>
    </div>
</div>
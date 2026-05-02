<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/kasir');
})->name('home');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('/kelola-produk', 'pages::kelola-produk.index')->name('kelola-produk');

    Route::livewire('/kelola-produk/kategori', 'pages::kelola-produk.kategori.index');
    Route::livewire('/kelola-produk/kategori/create', 'pages::kelola-produk.kategori.create');
    Route::livewire('/kelola-produk/kategori/edit/{uuid}', 'pages::kelola-produk.kategori.edit');

    Route::livewire('/kelola-produk/discount', 'pages::kelola-produk.discount.index');
    Route::livewire('/kelola-produk/discount/create', 'pages::kelola-produk.discount.create');
    Route::livewire('/kelola-produk/discount/edit/{uuid}', 'pages::kelola-produk.discount.edit');

    Route::livewire('/kelola-produk/produk', 'pages::kelola-produk.produk.index');
    Route::livewire('/kelola-produk/produk/create', 'pages::kelola-produk.produk.create');
    Route::livewire('/kelola-produk/produk/edit/{uuid}', 'pages::kelola-produk.produk.edit');

    Route::livewire('/kelola-toko', 'pages::kelola-toko.index')->name('kelola-toko');

    Route::livewire('/kelola-toko/toko', 'pages::kelola-toko.toko.index')->name('kelola-toko.toko.index');

    Route::livewire('/kelola-toko/metode-pembayaran', 'pages::kelola-toko.metodepembayaran.index');
    Route::livewire('/kelola-toko/metode-pembayaran/create', 'pages::kelola-toko.metodepembayaran.create');
    Route::livewire('/kelola-toko/metode-pembayaran/edit/{uuid}', 'pages::kelola-toko.metodepembayaran.edit');

    Route::livewire('/kasir', 'pages::transaksi.kasir')->name('kasir');
    Route::livewire('/checkout', 'pages::transaksi.checkout')->name('checkout');
    Route::livewire('/payment/{uuid?}', 'pages::transaksi.payment')->name('payment');
    Route::livewire('/order/success/{uuid}', 'pages::transaksi.success')->name('order.success');
    Route::livewire('/order/fail', 'pages::transaksi.fail');

    Route::livewire('/transaksi', 'pages::transaksi.transaksi')->name('transaksi');
    Route::livewire('/transaksi/{uuid}', 'pages::transaksi.transaksidetail');

    Route::livewire('/report', 'pages::transaksi.report.index')->name('report');
    Route::livewire('/report/transaksi', 'pages::transaksi.report.transaksi');

    Route::livewire('/version', 'pages::version')->name('version');
});

require __DIR__ . '/settings.php';

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('idTransaksi');
            $table->string('discount_name')->nullable();
            $table->string('namaPelanggan')->nullable();
            $table->string('note')->nullable();
            $table->integer('subtotal');
            $table->integer('discount')->nullable();
            $table->integer('total');
            $table->enum('statusPayment', ['Pending', 'Paid'])->default('Pending');
            $table->enum('statusOrder', ['Process', 'Done'])->default('Process');
            $table->string('payment_method')->nullable();
            $table->integer('paid_amount')->nullable();
            $table->integer('change')->nullable();
            $table->string('cashier');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};

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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('billname');
            $table->string('anvis_invoice');
            $table->date('anvis_invoice_date');
            $table->string('anvis_status');
            $table->string('vendor_invoice');
            $table->date('vendor_invoice_date');
            $table->string('vendor_status');
            $table->decimal('external_amount',12, 2);
            $table->string('executive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};

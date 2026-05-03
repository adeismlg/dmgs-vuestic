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
        Schema::create('daily_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->date('sale_date'); // Tanggal transaksi
            $table->decimal('amount', 15, 2); // Total omzet hari itu
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamps();

            // Mencegah double input pada tanggal yang sama untuk member yang sama
            $table->unique(['member_id', 'sale_date']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_sales');
    }
};

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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // 1. Identitas Pesanan
            $table->string('invoice_number')->unique(); // Contoh: INV-20231027-ABC
            
            // 2. Relasi UMKM (Siapa yang mengerjakan produk ini?)
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            
            // 3. Relasi Pelanggan (Nullable jika Pelanggan adalah Guest/Tanpa Akun)
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
            
            // 4. Data Pelanggan (Tetap disimpan untuk kemudahan kontak WA & arsip Admin)
            $table->string('customer_name');
            $table->string('customer_whatsapp'); // Format: 628xxx (tanpa + atau spasi)
            
            // 5. Data Keuangan
            $table->decimal('total_amount', 15, 2); // Harga Produk
            $table->decimal('shipping_cost', 15, 2)->default(0); // Ongkos Kirim
            $table->text('notes')->nullable(); // Catatan pembeli (contoh: "ukuran L")
            
            // 6. Bukti Pengiriman (Evidence)
            $table->string('shipping_receipt')->nullable(); // Nomor Resi / Link Foto Struk
            
            // 7. State Machine (Status Alur Kerja)
            $table->enum('status', [
                'pending_member',  // Admin input, menunggu Member klik 'Terima'
                'waiting_payment', // Member sudah sedia, menunggu Pembeli Bayar
                'processing',      // Admin sudah terima uang, Member mulai packing/produksi
                'shipped',         // Member sudah kirim & input resi
                'completed',       // Pembeli konfirmasi terima barang
                'canceled'         // Pesanan dibatalkan
            ])->default('pending_member');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bagian tambahan (section ke-2 dst) pada kartu pelaporan hasil perawatan.
     *
     * Satu form laporan perawatan dapat memuat hingga dua "bagian" laporan.
     * Bagian pertama tetap tersimpan pada kolom utama tabel maintenance_reports,
     * sedangkan bagian tambahan disimpan pada tabel ini agar data tiap bagian
     * tidak tercampur (mis. aset + bahan/foto bagian 1 terpisah dari bagian 2).
     */
    public function up(): void
    {
        Schema::create('maintenance_report_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_report_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('bagian')->default(2);
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('jenis_pekerjaan');
            $table->text('uraian_pekerjaan')->nullable();
            $table->date('tanggal_pelaksanaan')->nullable();
            $table->decimal('biaya', 15, 2)->default(0);
            $table->decimal('biaya_jasa', 15, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['maintenance_report_id', 'bagian']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_report_items');
    }
};

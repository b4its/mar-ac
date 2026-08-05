<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_reports', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_laporan')->unique();
            $table->foreignId('damage_report_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('asset_id')->constrained()->restrictOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pelapor_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('teknisi_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('jenis_pekerjaan');
            $table->text('uraian_pekerjaan');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->decimal('biaya', 15, 2)->nullable();
            $table->decimal('biaya_jasa', 15, 2)->nullable();
            $table->string('status');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_reports');
    }
};

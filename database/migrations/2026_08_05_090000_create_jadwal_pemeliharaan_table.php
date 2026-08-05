<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_pemeliharaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->date('tanggal_jadwal');
            $table->string('jenis_pekerjaan');
            $table->text('catatan')->nullable();
            $table->string('status')->default('terjadwal');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();

            $table->index(['tanggal_jadwal', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pemeliharaan');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damage_reports', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_laporan')->unique();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pelapor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tingkat_kerusakan', 20)->default('ringan');
            $table->string('jenis_kerusakan');
            $table->text('uraian_kerusakan')->nullable();
            $table->date('tanggal_laporan');
            $table->string('status', 20)->default('dilaporkan');
            $table->timestamp('approved_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['status', 'tanggal_laporan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damage_reports');
    }
};

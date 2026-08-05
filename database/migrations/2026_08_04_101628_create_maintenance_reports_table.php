<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_reports', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_laporan')->unique();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pelapor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verifikator_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approver_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('jenis_pekerjaan');
            $table->text('uraian_pekerjaan')->nullable();
            $table->date('tanggal_pelaksanaan')->nullable();
            $table->decimal('biaya', 15, 2)->default(0);
            $table->decimal('biaya_jasa', 15, 2)->default(0);
            $table->string('status', 20)->default('diajukan');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['status', 'tanggal_pelaksanaan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_reports');
    }
};

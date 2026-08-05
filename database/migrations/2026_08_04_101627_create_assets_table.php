<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('nama_alat');
            $table->string('jenis_alat')->nullable();
            $table->string('kode_alat')->nullable();
            $table->string('no_inventaris')->nullable();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('kapasitas')->nullable();
            $table->string('merk')->nullable();
            $table->string('tahun_pemakaian', 4)->nullable();
            $table->string('status', 20)->default('baik');
            $table->date('last_maintenance_date')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['nama_alat', 'kode_alat', 'no_inventaris']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('repair_reports', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->unique('damage_report_id');
            $table->renameColumn('tanggal_mulai', 'tanggal_pelaksanaan');
            $table->foreignId('verifikator_user_id')->nullable()->after('teknisi_user_id')->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable()->after('status');
        });

        DB::table('repair_reports')
            ->whereIn('status', ['direncanakan', 'ditugaskan', 'proses'])
            ->update(['status' => 'diajukan']);

        DB::table('repair_reports')
            ->where('status', 'selesai')
            ->update(['status' => 'disetujui', 'verified_at' => now()]);

        DB::table('repair_reports')
            ->where('status', 'ditolak')
            ->update(['status' => 'revisi']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_reports', function (Blueprint $table) {
            $table->dropForeign(['verifikator_user_id']);
            $table->dropColumn(['verifikator_user_id', 'verified_at']);
            $table->dropUnique(['damage_report_id']);
            $table->renameColumn('tanggal_pelaksanaan', 'tanggal_mulai');
            $table->index('status');
        });
    }
};

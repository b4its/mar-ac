<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->json('print_fields')->nullable()->after('catatan');
        });

        Schema::table('damage_reports', function (Blueprint $table) {
            $table->json('print_fields')->nullable()->after('catatan');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->dropColumn('print_fields');
        });

        Schema::table('damage_reports', function (Blueprint $table) {
            $table->dropColumn('print_fields');
        });
    }
};

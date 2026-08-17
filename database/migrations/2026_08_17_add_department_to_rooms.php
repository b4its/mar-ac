<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Add department_id column
            $table->unsignedBigInteger('department_id')->nullable()->after('building_id');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });

        // Copy building_id values to department_id for matching records
        DB::statement("
            UPDATE rooms r 
            INNER JOIN departments d ON d.building_id = r.building_id 
            SET r.department_id = d.id 
            WHERE r.department_id IS NULL
            LIMIT 1;
        ");
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });
    }
};

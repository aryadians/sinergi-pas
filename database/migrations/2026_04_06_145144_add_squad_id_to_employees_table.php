<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('squad_id')->nullable()->constrained('squads')->onDelete('set null')->after('work_unit_id');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['squad_id']);
            $table->dropColumn('squad_id');
        });
    }
};

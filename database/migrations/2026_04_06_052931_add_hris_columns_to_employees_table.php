<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('rank_class')->nullable()->after('rank'); // II, III, IV
            $table->enum('employee_type', ['regu_jaga', 'non_regu_jaga'])->default('non_regu_jaga')->after('rank_class');
            $table->string('picket_regu')->nullable()->after('employee_type'); // A, B, C, D
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['rank_class', 'employee_type', 'picket_regu']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->boolean('is_cpns')->default(false)->after('rank_id');
            $table->boolean('is_tubel')->default(false)->after('is_cpns');
            $table->foreignId('acting_tunkin_id')->nullable()->constrained('tunkins')->onDelete('set null')->after('is_tubel');
            $table->date('acting_start_date')->nullable()->after('acting_tunkin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['acting_tunkin_id']);
            $table->dropColumn(['is_cpns', 'is_tubel', 'acting_tunkin_id', 'acting_start_date']);
        });
    }
};

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
        Schema::table('attendances', function (Blueprint $table) {
            $table->time('check_in_2')->nullable()->after('status');
            $table->time('check_out_2')->nullable()->after('check_in_2');
            $table->integer('late_minutes_2')->default(0)->after('check_out_2');
            $table->integer('early_minutes_2')->default(0)->after('late_minutes_2');
            $table->string('status_2')->default('absent')->after('early_minutes_2');
            $table->decimal('allowance_amount_2', 15, 2)->default(0)->after('status_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'check_in_2',
                'check_out_2',
                'late_minutes_2',
                'early_minutes_2',
                'status_2',
                'allowance_amount_2'
            ]);
        });
    }
};

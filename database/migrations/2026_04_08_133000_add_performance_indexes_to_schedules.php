<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->index(['schedule_type_id', 'date']);
            $table->index('date');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->index(['status', 'date']);
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropIndex(['schedule_type_id', 'date']);
            $table->dropIndex(['date']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['status', 'date']);
        });
    }
};

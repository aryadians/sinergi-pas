<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('squad_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('squad_id')->constrained()->onDelete('cascade');
            $table->foreignId('shift_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->timestamps();
            
            $table->unique(['shift_id', 'date']); // One shift per date can only have one squad
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('squad_schedules');
    }
};

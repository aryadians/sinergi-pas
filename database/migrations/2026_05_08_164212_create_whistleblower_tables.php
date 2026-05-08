<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whistleblower_reports', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_anonymous')->default(false);
            $table->string('category');
            $table->text('description');
            $table->string('status')->default('pending'); // pending, investigating, resolved, rejected
            $table->text('admin_response')->nullable();
            $table->timestamps();
        });

        Schema::create('whistleblower_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('whistleblower_reports')->cascadeOnDelete();
            $table->longText('file_path'); // Will hold base64 for images or file paths for video/audio
            $table->string('file_type'); // image, video, audio, document
            $table->string('original_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whistleblower_evidences');
        Schema::dropIfExists('whistleblower_reports');
    }
};

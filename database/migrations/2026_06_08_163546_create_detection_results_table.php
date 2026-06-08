<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detection_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_profile_id')
                  ->constrained('user_profiles')
                  ->onDelete('cascade');
            $table->string('abr_result', 10);
            $table->string('tbr_result', 10);
            $table->string('cbr_result', 10);
            $table->string('final_gender', 10);
            $table->unsignedTinyInteger('confidence');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detection_results');
    }
};

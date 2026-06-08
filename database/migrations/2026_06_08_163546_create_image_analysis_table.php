<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('image_analysis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_profile_id')
                  ->constrained('user_profiles')
                  ->onDelete('cascade');
            $table->string('hair_feature', 20);
            $table->boolean('is_hijab_detected');
            $table->boolean('has_facial_hair');
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->string('cbr_result', 10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_analysis');
    }
};

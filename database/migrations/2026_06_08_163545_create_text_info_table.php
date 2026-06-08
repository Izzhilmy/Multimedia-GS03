<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('text_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_profile_id')
                  ->constrained('user_profiles')
                  ->onDelete('cascade');
            $table->string('honorific_title', 20)->nullable();
            $table->string('name_keyword', 50)->nullable();
            $table->string('tbr_result', 10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('text_info');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('id_card_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_profile_id')
                  ->constrained('user_profiles')
                  ->onDelete('cascade');
            $table->string('ic_gender', 10);
            $table->string('abr_result', 10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('id_card_info');
    }
};

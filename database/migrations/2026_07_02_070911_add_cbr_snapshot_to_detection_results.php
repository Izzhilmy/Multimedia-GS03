<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detection_results', function (Blueprint $table) {
            $table->string('hair_feature', 20)->nullable()->after('image_path');
            $table->boolean('is_hijab_detected')->default(false)->after('hair_feature');
            $table->boolean('has_facial_hair')->default(false)->after('is_hijab_detected');
        });

        DB::statement('DROP VIEW IF EXISTS student_detection_summary');

        DB::statement("
            CREATE VIEW student_detection_summary AS
            SELECT
                dr.id                AS result_id,
                up.matric_no,
                dr.full_name,
                dr.ic_number,
                dr.image_path,
                dr.hair_feature,
                dr.is_hijab_detected,
                dr.has_facial_hair,
                dr.abr_result,
                dr.tbr_result,
                dr.cbr_result,
                dr.final_gender,
                dr.confidence,
                dr.created_at        AS detected_at
            FROM detection_results dr
            JOIN user_profiles up ON up.id = dr.user_profile_id
            ORDER BY dr.created_at DESC
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS student_detection_summary');

        Schema::table('detection_results', function (Blueprint $table) {
            $table->dropColumn(['hair_feature', 'is_hijab_detected', 'has_facial_hair']);
        });

        DB::statement("
            CREATE VIEW student_detection_summary AS
            SELECT
                dr.id           AS result_id,
                up.matric_no,
                dr.full_name,
                dr.ic_number,
                dr.image_path,
                dr.abr_result,
                dr.tbr_result,
                dr.cbr_result,
                dr.final_gender,
                dr.confidence,
                dr.created_at   AS detected_at
            FROM detection_results dr
            JOIN user_profiles up ON up.id = dr.user_profile_id
            ORDER BY dr.created_at DESC
        ");
    }
};

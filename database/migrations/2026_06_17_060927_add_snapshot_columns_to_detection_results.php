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
            $table->string('full_name', 255)->after('user_profile_id')->default('');
            $table->string('ic_number', 20)->after('full_name')->default('');
            $table->string('image_path', 500)->nullable()->after('ic_number');
        });

        DB::statement('DROP VIEW IF EXISTS student_detection_summary');

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

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS student_detection_summary');

        Schema::table('detection_results', function (Blueprint $table) {
            $table->dropColumn(['full_name', 'ic_number', 'image_path']);
        });

        DB::statement("
            CREATE VIEW student_detection_summary AS
            SELECT
                dr.id           AS result_id,
                up.matric_no,
                up.full_name,
                up.ic_number,
                up.image_path,
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE INDEX idx_user_profiles_matric_no
                       ON user_profiles(matric_no)');

        DB::statement('CREATE INDEX idx_detection_results_user_profile_id
                       ON detection_results(user_profile_id)');

        DB::statement('CREATE INDEX idx_detection_results_final_gender
                       ON detection_results(final_gender)');

        DB::statement('CREATE INDEX idx_image_analysis_user_profile_id
                       ON image_analysis(user_profile_id)');

        DB::statement("
            CREATE VIEW student_detection_summary AS
            SELECT
                dr.id                   AS result_id,
                up.matric_no,
                up.full_name,
                up.ic_number,
                up.image_path,
                dr.abr_result,
                dr.tbr_result,
                dr.cbr_result,
                dr.final_gender,
                dr.confidence,
                dr.created_at           AS detected_at
            FROM detection_results dr
            JOIN user_profiles up ON up.id = dr.user_profile_id
            ORDER BY dr.created_at DESC
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS student_detection_summary');
        DB::statement('DROP INDEX idx_image_analysis_user_profile_id ON image_analysis');
        DB::statement('DROP INDEX idx_detection_results_final_gender ON detection_results');
        DB::statement('DROP INDEX idx_detection_results_user_profile_id ON detection_results');
        DB::statement('DROP INDEX idx_user_profiles_matric_no ON user_profiles');
    }
};

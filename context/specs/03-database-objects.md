# Unit 03: Database Objects

## Goal

Create useful MySQL database objects for the gs03 database:
indexes for query performance and a view for the detection
history listing. All objects created in a single migration.

---

## Design

Pure backend. One migration file. No UI.
All objects target the `mysql` (gs03) connection only.

---

## Implementation

### Migration File

```bash
php artisan make:migration create_gs03_database_objects
```

All objects go in this single migration's `up()` method using
`DB::statement()`. Drop them in `down()`.

---

### Indexes

Add indexes on the most frequently queried columns:

```php
// Speed up fetching a student's profile by matric_no (used on every login)
DB::statement('CREATE INDEX idx_user_profiles_matric_no
               ON user_profiles(matric_no)');

// Speed up fetching a student's detection history (ordered by latest)
DB::statement('CREATE INDEX idx_detection_results_user_profile_id
               ON detection_results(user_profile_id)');

// Speed up filtering detection results by final_gender
DB::statement('CREATE INDEX idx_detection_results_final_gender
               ON detection_results(final_gender)');

// Speed up image_analysis lookup by user_profile_id
DB::statement('CREATE INDEX idx_image_analysis_user_profile_id
               ON image_analysis(user_profile_id)');
```

---

### View: student_detection_summary

A view that joins user_profiles and detection_results to
produce a flat summary row per detection — useful for the
history page.

```php
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
```

---

### down() Method

```php
DB::statement('DROP VIEW IF EXISTS student_detection_summary');
DB::statement('DROP INDEX idx_image_analysis_user_profile_id ON image_analysis');
DB::statement('DROP INDEX idx_detection_results_final_gender ON detection_results');
DB::statement('DROP INDEX idx_detection_results_user_profile_id ON detection_results');
DB::statement('DROP INDEX idx_user_profiles_matric_no ON user_profiles');
```

---

### Run the Migration

```bash
php artisan migrate
```

Verify in phpMyAdmin that indexes and view appear in gs03.

---

## Dependencies

Unit 02 must be complete (all tables must exist).

---

## Verify When Done

- [ ] `idx_user_profiles_matric_no` index exists on user_profiles
- [ ] `idx_detection_results_user_profile_id` index exists
- [ ] `idx_detection_results_final_gender` index exists
- [ ] `idx_image_analysis_user_profile_id` index exists
- [ ] `student_detection_summary` view exists in gs03
- [ ] View query runs without error: `SELECT * FROM student_detection_summary LIMIT 5`
- [ ] `php artisan migrate:status` shows this migration as Ran
- [ ] `context/progress-tracker.md` updated to mark Unit 03 complete

# Unit 02: Database Migrations

## Goal

Create all gs03 tables via Laravel migrations and run them successfully.
All tables must exist with correct columns, types, and foreign key
constraints before any model or feature work begins.

Do NOT touch mmdb2026. All migrations target the `mysql` (gs03) connection.

---

## Design

Pure backend. No UI. No models yet — just migrations.
Run each migration individually to isolate failures.
Migration order must follow foreign key dependencies.

---

## Implementation

### Migration Order

```
1. user_profiles         ← base table, no FK
2. id_card_info          ← FK: user_profiles
3. text_info             ← FK: user_profiles
4. image_analysis        ← FK: user_profiles
5. detection_results     ← FK: user_profiles
```

---

### Table: user_profiles

Stores the logged-in student's identity snapshot from mmdb2026.stu.
Created on first login if not already present.

```bash
php artisan make:migration create_user_profiles_table
```

```php
Schema::create('user_profiles', function (Blueprint $table) {
    $table->id();
    $table->string('matric_no', 20)->unique();   // from mmdb2026.stu
    $table->string('full_name', 255);             // from mmdb2026.stu
    $table->string('ic_number', 20)->nullable();  // entered by student
    $table->string('image_path', 500)->nullable();// uploaded photo path
    $table->timestamps();
});
```

---

### Table: id_card_info (ABR data)

Stores IC-derived gender for Attribute-Based Retrieval.

```bash
php artisan make:migration create_id_card_info_table
```

```php
Schema::create('id_card_info', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_profile_id')
          ->constrained('user_profiles')
          ->onDelete('cascade');
    $table->string('ic_gender', 10);   // 'Male' or 'Female' derived from IC
    $table->string('abr_result', 10);  // 'Male' or 'Female'
    $table->timestamps();
});
```

---

### Table: text_info (TBR data)

Stores name keyword analysis for Text-Based Retrieval.

```bash
php artisan make:migration create_text_info_table
```

```php
Schema::create('text_info', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_profile_id')
          ->constrained('user_profiles')
          ->onDelete('cascade');
    $table->string('honorific_title', 20)->nullable();  // Mr, Mrs, Ms, etc.
    $table->string('name_keyword', 50)->nullable();      // 'bin', 'binti', etc.
    $table->string('tbr_result', 10);                   // 'Male' or 'Female'
    $table->timestamps();
});
```

---

### Table: image_analysis (CBR data)

Stores visual feature inputs and Content-Based Retrieval result.

```bash
php artisan make:migration create_image_analysis_table
```

```php
Schema::create('image_analysis', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_profile_id')
          ->constrained('user_profiles')
          ->onDelete('cascade');
    $table->string('hair_feature', 20);       // 'Short', 'Long', 'Medium'
    $table->boolean('is_hijab_detected');     // true or false
    $table->boolean('has_facial_hair');       // true or false
    $table->decimal('confidence_score', 5, 2)->nullable(); // e.g. 87.00
    $table->string('cbr_result', 10);         // 'Male' or 'Female'
    $table->timestamps();
});
```

---

### Table: detection_results

Fused final result from all three retrieval methods.
One row per detection submission.

```bash
php artisan make:migration create_detection_results_table
```

```php
Schema::create('detection_results', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_profile_id')
          ->constrained('user_profiles')
          ->onDelete('cascade');
    $table->string('abr_result', 10);    // 'Male' or 'Female'
    $table->string('tbr_result', 10);    // 'Male' or 'Female'
    $table->string('cbr_result', 10);    // 'Male' or 'Female'
    $table->string('final_gender', 10);  // 'Male' or 'Female' (majority vote)
    $table->unsignedTinyInteger('confidence'); // 33, 67, or 100
    $table->timestamps();
});
```

---

### Run All Migrations

```bash
php artisan migrate
```

Confirm all 5 tables exist in gs03 via phpMyAdmin or:

```bash
php artisan migrate:status
```

---

## Dependencies

Unit 01 must be complete (database connections configured).

---

## Verify When Done

- [ ] `user_profiles` table exists with correct columns
- [ ] `id_card_info` table exists, FK to user_profiles
- [ ] `text_info` table exists, FK to user_profiles
- [ ] `image_analysis` table exists, FK to user_profiles
- [ ] `detection_results` table exists, FK to user_profiles
- [ ] `php artisan migrate:status` shows all migrations as Ran
- [ ] mmdb2026 is untouched — no new tables created there
- [ ] `context/progress-tracker.md` updated to mark Unit 02 complete

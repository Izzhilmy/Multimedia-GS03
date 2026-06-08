# Progress Tracker — Gender Detection System (GS03)

Update this file after every meaningful implementation change.
Claude reads this at the start of every session to understand
current state without re-reading all docs.

---

## Current Phase

Unit 03 complete. Ready for Unit 04.

---

## Current Goal

Complete Unit 04: Auth — cross-DB login, session, middleware.

---

## Build Order

Units are ordered by dependency. Never skip ahead.

| # | Unit | Status |
|---|---|---|
| 01 | Project setup — Laravel, dual DB config, folder structure | ✅ Complete |
| 02 | Database migrations — gs03 tables | ✅ Complete |
| 03 | Database objects — indexes, views | ✅ Complete |
| 04 | Auth — cross-DB login against mmdb2026.stu, session, middleware | ⬜ Not started |
| 05 | Gender detection — form, ABR+TBR+CBR+fusion, result page | ⬜ Not started |
| 06 | History — student's past detection results | ⬜ Not started |

---

## Open Questions

- ✅ `mmdb2026.stu.password` confirmed plain text. Use direct string comparison in AuthService.
- Confirm whether CBR uses AI image model or rule-based visual feature inputs
  (current spec assumes student fills in visual feature fields manually,
  matching the UI design in the presentation slides).

## Local mmdb2026 Simulation

`mmdb2026.stu` recreated locally to match real schema (confirmed from screenshot).
Full columns: `id`, `matric_no`, `full_name`, `phone_no`, `group_no`, `life_motto`,
`password`, `photoStu`, `photoStu_date`, `docStu`, `docStu_date`.
Password is plain text (values: NULL, 'qwerty', '123').
Auth code must handle NULL password (treat as login disabled).
11 real students seeded from the actual class DB screenshot.
Use matric_no `B032420099` / password `123` for local dev testing.

---

## Completed Units

### Unit 01 — Project Setup
- Laravel 12 installed, app key generated
- Dual DB: `mysql` → gs03 (user GS03/1234), `mmdb` → mmdb2026 (root/password)
- SESSION_DRIVER changed to `file` (no default Laravel tables)
- Folder structure: app/Services, app/Http/Middleware, resources/views/{auth,detection,history,layouts}, storage/app/public/uploads
- Placeholder login view at resources/views/auth/login.blade.php
- `/login` route renders; `/` redirects to login
- `php artisan storage:link` done
- Both DB connections verified (`{"gs03":{"1":1},"mmdb":{"1":1}}`), /db-check route removed

### Unit 03 — Database Objects
- 4 indexes: idx_user_profiles_matric_no, idx_detection_results_user_profile_id, idx_detection_results_final_gender, idx_image_analysis_user_profile_id
- View: student_detection_summary (joins user_profiles + detection_results, ordered by latest)
- All objects verified in gs03 via information_schema

### Unit 02 — Database Migrations
- 5 tables created in gs03: user_profiles, id_card_info, text_info, image_analysis, detection_results
- Foreign key constraints: all child tables cascade-delete from user_profiles
- Default Laravel migrations (users, cache, jobs) left Pending — not used by this project
- mmdb2026 untouched — verified no new tables
- GS03 MySQL user confirmed able to connect and owns all gs03 tables

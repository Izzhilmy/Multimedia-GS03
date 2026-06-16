# Progress Tracker — Gender Detection System (GS03)

Update this file after every meaningful implementation change.
Claude reads this at the start of every session to understand
current state without re-reading all docs.

---

## Current Phase

All 6 units complete. Project fully implemented.

---

## Current Goal

All units done. Verify, polish, or submit as required.

---

## Build Order

Units are ordered by dependency. Never skip ahead.

| # | Unit | Status |
|---|---|---|
| 01 | Project setup — Laravel, dual DB config, folder structure | ✅ Complete |
| 02 | Database migrations — gs03 tables | ✅ Complete |
| 03 | Database objects — indexes, views | ✅ Complete |
| 04 | Auth — cross-DB login against mmdb2026.stu, session, middleware | ✅ Complete |
| 05 | Gender detection — form, ABR+TBR+CBR+fusion, result page | ✅ Complete |
| 06 | History — student's past detection results | ✅ Complete |

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

### Unit 05 — Gender Detection
- 5 services: AbrService (IC last digit), TbrService (name keywords), CbrService (visual features/scoring), DetectionFusionService (majority vote), DetectionResultService (gs03 persistence)
- 5 models: UserProfile, IdCardInfo, TextInfo, ImageAnalysis, DetectionResult
- DetectionController: showForm, analyze (runs all 3 + fusion + save + flash), showResult
- Detection form: 2-col grid, photo upload with live preview, "Enter" preview button, dark-navy ui-design.md styling
- Result page: 3-col grid, green retrieval cards, cyan final card, male/female badges, ghost/primary action buttons
- Verified: all 3 retrievals run, 5 tables populated, result page renders with correct Male/Female output

### Unit 04 — Auth
- StuUser model (mmdb connection, stu table, read-only)
- AuthService: plain-text password check, NULL password guard
- AuthController: showLogin, login (session + regenerate), logout
- AuthMiddleware registered as `student.auth` alias
- Routes: GET/POST /login, POST /logout, all protected routes under middleware group
- Login view: full dark-navy UI per ui-design.md (Cinzel/Lato, deco border, cream palette)
- Layout: app.blade.php with navbar (matric_no, active links, logout)
- Stub controllers/views for Detection and History (Units 05/06)
- Verified: /login 200, /detection redirect→login, bad creds rejected, good creds→/detection, matric shown in nav

### Unit 03 — Database Objects
- 4 indexes: idx_user_profiles_matric_no, idx_detection_results_user_profile_id, idx_detection_results_final_gender, idx_image_analysis_user_profile_id
- View: student_detection_summary (joins user_profiles + detection_results, ordered by latest)
- All objects verified in gs03 via information_schema

### Unit 06 — History
- HistoryController queries `student_detection_summary` view filtered by `session('student.matric_no')`, paginated 10
- History view: dark-navy card, overflow:hidden table, badge-male/badge-female, confidence bar (cyan fill), Carbon date format
- Empty state with link to /detection; `$results->links()` pagination (only shown if hasPages)
- Verified: /history unauthenticated → 302 login, logged-in student sees own rows, different student sees empty state

### Unit 02 — Database Migrations
- 5 tables created in gs03: user_profiles, id_card_info, text_info, image_analysis, detection_results
- Foreign key constraints: all child tables cascade-delete from user_profiles
- Default Laravel migrations (users, cache, jobs) left Pending — not used by this project
- mmdb2026 untouched — verified no new tables
- GS03 MySQL user confirmed able to connect and owns all gs03 tables

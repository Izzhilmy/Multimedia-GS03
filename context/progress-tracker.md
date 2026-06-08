# Progress Tracker — Gender Detection System (GS03)

Update this file after every meaningful implementation change.
Claude reads this at the start of every session to understand
current state without re-reading all docs.

---

## Current Phase

Unit 02 complete. Ready for Unit 03.

---

## Current Goal

Complete Unit 03: Database Objects (indexes, views).

---

## Build Order

Units are ordered by dependency. Never skip ahead.

| # | Unit | Status |
|---|---|---|
| 01 | Project setup — Laravel, dual DB config, folder structure | ✅ Complete |
| 02 | Database migrations — gs03 tables | ✅ Complete |
| 03 | Database objects — indexes, views | ⬜ Not started |
| 04 | Auth — cross-DB login against mmdb2026.stu, session, middleware | ⬜ Not started |
| 05 | Gender detection — form, ABR+TBR+CBR+fusion, result page | ⬜ Not started |
| 06 | History — student's past detection results | ⬜ Not started |

---

## Open Questions

- Confirm whether `mmdb2026.stu.password` is plain text or hashed.
  If hashed, switch AuthService to use `Hash::check()`.
- Confirm exact columns available in `mmdb2026.stu`
  (need: matric_no, full_name, password — check for phone_no, group_no).
- Confirm whether CBR uses AI image model or rule-based visual feature inputs
  (current spec assumes student fills in visual feature fields manually,
  matching the UI design in the presentation slides).

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

### Unit 02 — Database Migrations
- 5 tables created in gs03: user_profiles, id_card_info, text_info, image_analysis, detection_results
- Foreign key constraints: all child tables cascade-delete from user_profiles
- Default Laravel migrations (users, cache, jobs) left Pending — not used by this project
- mmdb2026 untouched — verified no new tables
- GS03 MySQL user confirmed able to connect and owns all gs03 tables

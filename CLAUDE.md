# CLAUDE.md — Gender Detection System (GS03)

These rules apply to every task in this project unless explicitly overridden.
Bias: caution over speed on non-trivial work.

---

## Rule 1 — Think Before Coding
State assumptions explicitly. Ask rather than guess.
Push back when a simpler approach exists. Stop when confused.

## Rule 2 — Simplicity First
Minimum code that solves the problem. Nothing speculative.
No abstractions for single-use code.

## Rule 3 — Surgical Changes
Touch only what you must. Don't improve adjacent code.
Match existing style. Don't refactor what isn't broken.

## Rule 4 — Goal-Driven Execution
Define success criteria. Loop until verified.
Strong success criteria let Claude loop independently.

## Rule 5 — Read Before You Write
Before adding code, read the relevant controller, model, and routes.
If unsure why existing code is structured a certain way, ask.

## Rule 6 — Fail Loud
"Completed" is wrong if anything was skipped silently.
Default to surfacing uncertainty, not hiding it.

## Rule 7 — Match the Codebase's Conventions, Even If You Disagree
Conformance > taste inside the codebase.
If you think a convention is harmful, surface it. Don't fork silently.

---

## Read These Files in Order at the Start of Every Session

1. `context/project-overview.md` — what this is, who uses it, scope
2. `context/architecture.md` — stack, boundaries, databases, invariants
3. `context/code-standards.md` — naming, conventions, file rules
4. `context/progress-tracker.md` — what is built, what is next, open questions

Update `context/progress-tracker.md` after every meaningful change.

---

## Then Read the Spec File for the Current Unit

Spec files live in `context/specs/`.
Read the spec for the current unit before writing any code.
Implement exactly as specified. Nothing beyond scope.

```
context/specs/
├── 01-project-setup.md
├── 02-database-migrations.md
├── 03-database-objects.md
├── 04-auth.md
├── 05-gender-detection.md
├── 06-history.md
```

---

## Project-Specific Critical Rules

- No single file exceeds 150 lines of code
- Controllers orchestrate only — all business logic lives in Services
- No Sanctum — session-based auth only
- No public registration — students log in via mmdb2026.stu credentials
- Login uses a CROSS DATABASE query: authenticate against `mmdb2026`.`stu`
- Any data not in mmdb2026.stu (detection results, images, IC card info) lives in `gs03` DB
- The `gs03` database password is `1234`
- Never store mmdb2026 credentials in the gs03 database
- Image uploads stored under `storage/app/public/uploads/`
- Detection results are always saved to gs03 after analysis — never shown only
- All three retrieval methods (ABR, TBR, CBR) must run and fuse into one final result
- All environment values in .env, never hardcoded

---

## Two-Database Architecture

| Database | Owner | Used For |
|---|---|---|
| `mmdb2026` | Shared class DB | Authentication only — read `stu` table |
| `gs03` | This project | All detection data, results, image paths |

Configure both connections in `config/database.php`.
The `mmdb2026` connection is **read-only** from this application's perspective.
Never write to `mmdb2026`.

---

## Local Development Databases

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gs03
DB_USERNAME=root
DB_PASSWORD=1234

DB_MMDB_HOST=127.0.0.1
DB_MMDB_PORT=3306
DB_MMDB_DATABASE=mmdb2026
DB_MMDB_USERNAME=root
DB_MMDB_PASSWORD=1234
```

---

## Commit Strategy

Use conventional commits:

  <type>(<scope>): <short imperative summary>

Types: feat | fix | refactor | test | chore | docs | style
Scope = the unit or module touched (auth, detection, history, db, etc.)
Subject line ≤ 72 characters, imperative mood.
Never commit: .env, secrets, debug logs, commented-out code.

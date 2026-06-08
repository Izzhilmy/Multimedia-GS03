# Architecture — Gender Detection System (GS03)

## Stack

| Layer | Technology |
|---|---|
| Backend | Laravel (PHP) |
| Frontend | Blade templates (plain PHP views) |
| Styling | Bootstrap or plain CSS — match existing project style |
| Database (auth) | MySQL — mmdb2026.stu (read-only) |
| Database (app) | MySQL — gs03 (read/write) |
| File storage | Laravel local disk — storage/app/public/uploads/ |
| Session | Laravel session (database or file driver) |

---

## Two-Database Connection Setup

Define two named connections in `config/database.php`:

```php
'mysql' => [
    'driver'    => 'mysql',
    'host'      => env('DB_HOST', '127.0.0.1'),
    'port'      => env('DB_PORT', '3306'),
    'database'  => env('DB_DATABASE', 'gs03'),
    'username'  => env('DB_USERNAME', 'root'),
    'password'  => env('DB_PASSWORD', '1234'),
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
    'strict'    => true,
],

'mmdb' => [
    'driver'    => 'mysql',
    'host'      => env('DB_MMDB_HOST', '127.0.0.1'),
    'port'      => env('DB_MMDB_PORT', '3306'),
    'database'  => env('DB_MMDB_DATABASE', 'mmdb2026'),
    'username'  => env('DB_MMDB_USERNAME', 'root'),
    'password'  => env('DB_MMDB_PASSWORD', '1234'),
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
    'strict'    => true,
],
```

The `mmdb` connection is **read-only by convention** — never issue INSERT,
UPDATE, or DELETE against it.

---

## Authentication Flow

Login does NOT use Laravel's default `users` table.
Authentication is a manual credential check against `mmdb2026`.`stu`:

```
Student submits matric_no + password
  → AuthController
    → AuthService::attempt(matric_no, password)
      → DB::connection('mmdb')->table('stu')
           ->where('matric_no', matric_no)
           ->where('password', password)  ← plain text match (as stored in stu)
           ->first()
      → if found: session()->put('student', $stu_row)
      → redirect to /detection
    → if not found: back with error
```

**Important:** The `stu` table password column stores passwords as plain
text (as seen in mmdb2026.stu). Do not use Laravel's `Hash::check()` for
this — compare plain text directly.
If the password column turns out to be hashed, switch to `Hash::check()`
and document it in progress-tracker.md.

---

## Backend Architecture

### Controllers — Orchestrators Only

A controller method receives the request, calls a service, returns a view.
It must not contain business logic.

### Services — Business Logic Lives Here

| Service | Responsibility |
|---|---|
| `AuthService` | Cross-DB credential check, session management |
| `AbrService` | Attribute-Based Retrieval — gender from IC number |
| `TbrService` | Text-Based Retrieval — gender from name keywords |
| `CbrService` | Content-Based Retrieval — gender from image features |
| `DetectionFusionService` | Combine ABR+TBR+CBR into final result |
| `DetectionResultService` | Save result to gs03, retrieve history |

### Models

| Model | Connection | Table |
|---|---|---|
| `StuUser` | mmdb | stu |
| `UserProfile` | mysql (gs03) | user_profiles |
| `IdCardInfo` | mysql (gs03) | id_card_info |
| `TextInfo` | mysql (gs03) | text_info |
| `ImageAnalysis` | mysql (gs03) | image_analysis |
| `DetectionResult` | mysql (gs03) | detection_results |

---

## Folder Structure

```
app/
  Http/
    Controllers/
      AuthController.php
      DetectionController.php
      HistoryController.php
    Middleware/
      AuthMiddleware.php
  Services/
    AuthService.php
    AbrService.php
    TbrService.php
    CbrService.php
    DetectionFusionService.php
    DetectionResultService.php
  Models/
    StuUser.php
    UserProfile.php
    IdCardInfo.php
    TextInfo.php
    ImageAnalysis.php
    DetectionResult.php
resources/
  views/
    auth/
      login.blade.php
    detection/
      form.blade.php
      result.blade.php
    history/
      index.blade.php
    layouts/
      app.blade.php
routes/
  web.php
```

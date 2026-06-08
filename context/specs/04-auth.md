# Unit 04: Auth

## Goal

Students can log in using their matric_no and password from mmdb2026.stu.
Session is established on success. AuthMiddleware protects all non-login
routes. Logout clears the session.

---

## Design

No Laravel Auth facade. No users table. Full manual session auth.
Login form is a simple Blade view matching the project's dark navy style
(matching the GS03 presentation slides: dark navy `#1e2a4a` background,
cream/beige text, clean centered card).

---

## Implementation

### StuUser Model

Create `app/Models/StuUser.php`.
This model points to the `mmdb` connection and the `stu` table.
It is **read-only** — never call `save()` or `create()` on it.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StuUser extends Model
{
    protected $connection = 'mmdb';
    protected $table      = 'stu';
    public    $timestamps = false;  // stu table may not have timestamps

    protected $fillable = []; // read-only model
}
```

---

### AuthService

Create `app/Services/AuthService.php`:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AuthService
{
    /**
     * Attempt login against mmdb2026.stu.
     * Returns the student row array on success, null on failure.
     */
    public function execute(string $matricNo, string $password): ?object
    {
        $student = DB::connection('mmdb')
            ->table('stu')
            ->where('matric_no', $matricNo)
            ->first();

        if (!$student) {
            return null;
        }

        // stu.password is stored as plain text in mmdb2026
        // If it turns out to be hashed, switch to Hash::check() here
        // and document in progress-tracker.md
        if ($student->password !== $password) {
            return null;
        }

        return $student;
    }
}
```

---

### AuthController

Create `app/Http/Controllers/AuthController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function showLogin()
    {
        if (session()->has('student')) {
            return redirect()->route('detection.form');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'matric_no' => 'required|string|max:20',
            'password'  => 'required|string',
        ]);

        $student = $this->authService->execute(
            $request->input('matric_no'),
            $request->input('password')
        );

        if (!$student) {
            return back()->withErrors(['matric_no' => 'Invalid matric number or password.'])
                         ->withInput(['matric_no' => $request->input('matric_no')]);
        }

        session()->put('student', [
            'id'        => $student->id,
            'matric_no' => $student->matric_no,
            'full_name' => $student->full_name,
        ]);

        session()->regenerate();

        return redirect()->route('detection.form');
    }

    public function logout(Request $request)
    {
        session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
```

---

### AuthMiddleware

Create `app/Http/Middleware/AuthMiddleware.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('student')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
```

Register in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'student.auth' => \App\Http\Middleware\AuthMiddleware::class,
    ]);
})
```

---

### Routes

Replace the placeholder in `routes/web.php`:

```php
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DetectionController;
use App\Http\Controllers\HistoryController;
use Illuminate\Support\Facades\Route;

// Auth routes (no middleware)
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('student.auth')->group(function () {
    Route::get('/detection',       [DetectionController::class, 'showForm'])->name('detection.form');
    Route::post('/detection',      [DetectionController::class, 'analyze'])->name('detection.analyze');
    Route::get('/detection/result',[DetectionController::class, 'showResult'])->name('detection.result');
    Route::get('/history',         [HistoryController::class,   'index'])->name('history.index');
});

// Redirect root to login
Route::get('/', fn() => redirect()->route('login'));
```

---

### Login View

Create `resources/views/auth/login.blade.php`:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Gender Detection System</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #1e2a4a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: sans-serif;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 32px 28px;
            width: 340px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.3);
        }
        h2 {
            text-align: center;
            color: #1e2a4a;
            margin-bottom: 24px;
            font-size: 20px;
        }
        label { display: block; font-size: 13px; color: #555; margin-bottom: 4px; }
        input {
            width: 100%; padding: 10px 12px; border: 1px solid #ccc;
            border-radius: 8px; font-size: 14px; margin-bottom: 16px;
        }
        input:focus { outline: none; border-color: #1e2a4a; }
        .btn {
            width: 100%; padding: 12px; background: #1e2a4a;
            color: #fff; border: none; border-radius: 8px;
            font-size: 15px; cursor: pointer;
        }
        .btn:hover { background: #2e3f6a; }
        .error { color: red; font-size: 13px; margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Gender Detection System</h2>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <label for="matric_no">Matric Number</label>
            <input type="text" id="matric_no" name="matric_no"
                   value="{{ old('matric_no') }}" required autofocus>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn">Log In</button>
        </form>
    </div>
</body>
</html>
```

---

### Layout Stub

Create `resources/views/layouts/app.blade.php`:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gender Detection System</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; margin: 0; }
        nav {
            background: #1e2a4a; color: #fff; padding: 12px 24px;
            display: flex; justify-content: space-between; align-items: center;
        }
        nav a { color: #fff; text-decoration: none; margin-left: 16px; font-size: 14px; }
        .container { max-width: 900px; margin: 32px auto; padding: 0 16px; }
    </style>
</head>
<body>
    <nav>
        <span>Gender Detection System</span>
        <div>
            <span style="font-size:13px">{{ session('student.full_name') }}</span>
            <a href="{{ route('detection.form') }}">Detection</a>
            <a href="{{ route('history.index') }}">History</a>
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit"
                    style="background:none;border:none;color:#fff;cursor:pointer;font-size:14px;margin-left:16px">
                    Logout
                </button>
            </form>
        </div>
    </nav>
    <div class="container">
        @if(session('success'))
            <div style="background:#d4edda;color:#155724;padding:10px;border-radius:6px;margin-bottom:16px">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:6px;margin-bottom:16px">
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </div>
</body>
</html>
```

---

### Placeholder Detection View

Create `resources/views/detection/form.blade.php`:

```blade
@extends('layouts.app')
@section('content')
    <h2>Gender Detection — Unit 05 will complete this page.</h2>
@endsection
```

---

## Dependencies

Units 01–03 must be complete.

---

## Verify When Done

- [ ] `/login` renders the styled login page
- [ ] Correct matric_no + password → redirects to `/detection`
- [ ] Wrong credentials → stays on `/login` with error message
- [ ] Accessing `/detection` without login → redirects to `/login`
- [ ] Accessing `/history` without login → redirects to `/login`
- [ ] Logged-in student's full_name visible in nav bar
- [ ] Logout clears session and redirects to `/login`
- [ ] After logout, `/detection` redirects to `/login` again
- [ ] `context/progress-tracker.md` updated to mark Unit 04 complete

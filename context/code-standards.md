# Code Standards — Gender Detection System (GS03)

## PHP / Laravel Conventions

### Naming

| Type | Convention | Example |
|---|---|---|
| Controller | PascalCase + Controller | `DetectionController` |
| Service | PascalCase + Service | `AbrService` |
| Model | PascalCase singular | `DetectionResult` |
| Migration | snake_case descriptive | `create_detection_results_table` |
| Blade view | snake_case | `form.blade.php`, `login.blade.php` |

### Service Method Naming
Every service exposes a single primary public method named `execute()`:

```php
class AbrService
{
    public function execute(string $icNumber): array
    {
        // returns ['prediction' => 'Male'|'Female', 'ic_gender' => '...']
    }
}
```

### Controller Method Naming
Use standard names: `index`, `store`, `show`, `create`.
For non-resource actions use descriptive names: `showResult`, `logout`.

### Database
- Table names: snake_case plural (`detection_results`, `image_analysis`)
- Column names: snake_case (`ic_gender`, `abr_result`, `hair_feature`)
- Foreign keys: `{table_singular}_id` (`user_profile_id`)
- Boolean columns: prefix with `is_` (`is_hijab_detected`)
- Timestamps: always `created_at` and `updated_at`

---

## Session Convention

The authenticated student is stored in session as:

```php
session()->put('student', [
    'id'        => $stu->id,
    'matric_no' => $stu->matric_no,
    'full_name' => $stu->full_name,
]);
```

Access in controllers via `session('student')`.
AuthMiddleware checks `session()->has('student')`.

---

## Detection Result Array Contract

Every retrieval service returns the same shape:

```php
[
    'prediction' => 'Male' | 'Female',   // always one of these two strings
    'detail'     => [...],                // service-specific details
]
```

`DetectionFusionService::execute()` receives all three and returns:

```php
[
    'abr_result'   => 'Male' | 'Female',
    'tbr_result'   => 'Male' | 'Female',
    'cbr_result'   => 'Male' | 'Female',
    'final_gender' => 'Male' | 'Female',  // majority vote
    'confidence'   => int,                 // 33 | 67 | 100 (%)
]
```

---

## File Size Rule
No single file exceeds 150 lines. If growing beyond this, split it.

---

## Blade View Conventions
- Extend `layouts.app` in every view: `@extends('layouts.app')`
- Use `@section('content')` / `@endsection`
- No inline `<style>` blocks — use the shared stylesheet or Bootstrap classes
- Flash messages displayed via `session('success')` and `session('error')`

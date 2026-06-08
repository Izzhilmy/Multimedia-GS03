# Unit 06: History

## Goal

Authenticated student can view a paginated list of their own past
gender detection results at `/history`. Each row shows the submission
date, input name, all three retrieval results, and the final gender.
No student can see another student's data.

---

## Design

Single page reading from the `student_detection_summary` view in gs03.
Filtered strictly by the logged-in student's matric_no.
Newest results first.

---

## Implementation

### HistoryController

Create `app/Http/Controllers/HistoryController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function index()
    {
        $matricNo = session('student.matric_no');

        $results = DB::connection('mysql')
            ->table('student_detection_summary')
            ->where('matric_no', $matricNo)
            ->orderByDesc('detected_at')
            ->paginate(10);

        return view('history.index', compact('results'));
    }
}
```

---

### History View

Create `resources/views/history/index.blade.php`:

```blade
@extends('layouts.app')
@section('content')
<div style="background:#fff;border-radius:12px;padding:32px;box-shadow:0 2px 12px rgba(0,0,0,.1)">
    <h2 style="color:#1e2a4a;margin-bottom:24px">Detection History</h2>

    @if($results->isEmpty())
        <p style="color:#888;text-align:center;padding:32px">
            No detection results yet.
            <a href="{{ route('detection.form') }}">Run your first detection →</a>
        </p>
    @else
        <table style="width:100%;border-collapse:collapse;font-size:14px">
            <thead>
                <tr style="background:#1e2a4a;color:#fff">
                    <th style="padding:10px 12px;text-align:left">#</th>
                    <th style="padding:10px 12px;text-align:left">Name</th>
                    <th style="padding:10px 12px;text-align:left">IC</th>
                    <th style="padding:10px 12px;text-align:center">ABR</th>
                    <th style="padding:10px 12px;text-align:center">TBR</th>
                    <th style="padding:10px 12px;text-align:center">CBR</th>
                    <th style="padding:10px 12px;text-align:center">Final</th>
                    <th style="padding:10px 12px;text-align:center">Conf.</th>
                    <th style="padding:10px 12px;text-align:left">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $i => $row)
                <tr style="border-bottom:1px solid #eee;{{ $loop->even ? 'background:#f9f9f9' : '' }}">
                    <td style="padding:10px 12px">{{ $results->firstItem() + $i }}</td>
                    <td style="padding:10px 12px">{{ $row->full_name }}</td>
                    <td style="padding:10px 12px">{{ $row->ic_number }}</td>
                    <td style="padding:10px 12px;text-align:center">
                        <span style="padding:2px 10px;border-radius:12px;font-size:12px;
                            background:{{ $row->abr_result === 'Male' ? '#cfe2ff' : '#fce4ec' }};
                            color:{{ $row->abr_result === 'Male' ? '#084298' : '#880e4f' }}">
                            {{ $row->abr_result }}
                        </span>
                    </td>
                    <td style="padding:10px 12px;text-align:center">
                        <span style="padding:2px 10px;border-radius:12px;font-size:12px;
                            background:{{ $row->tbr_result === 'Male' ? '#cfe2ff' : '#fce4ec' }};
                            color:{{ $row->tbr_result === 'Male' ? '#084298' : '#880e4f' }}">
                            {{ $row->tbr_result }}
                        </span>
                    </td>
                    <td style="padding:10px 12px;text-align:center">
                        <span style="padding:2px 10px;border-radius:12px;font-size:12px;
                            background:{{ $row->cbr_result === 'Male' ? '#cfe2ff' : '#fce4ec' }};
                            color:{{ $row->cbr_result === 'Male' ? '#084298' : '#880e4f' }}">
                            {{ $row->cbr_result }}
                        </span>
                    </td>
                    <td style="padding:10px 12px;text-align:center">
                        <span style="padding:4px 14px;border-radius:12px;font-weight:bold;font-size:13px;
                            background:{{ $row->final_gender === 'Male' ? '#0d6efd' : '#d63384' }};
                            color:#fff">
                            {{ $row->final_gender }}
                        </span>
                    </td>
                    <td style="padding:10px 12px;text-align:center">{{ $row->confidence }}%</td>
                    <td style="padding:10px 12px;color:#888;font-size:13px">
                        {{ \Carbon\Carbon::parse($row->detected_at)->format('d M Y, H:i') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top:20px">
            {{ $results->links() }}
        </div>
    @endif
</div>
@endsection
```

---

## Security Note

The query in `HistoryController::index()` always filters by
`session('student.matric_no')`. Never expose a route that returns
results without this filter — a student must only ever see their own data.

---

## Dependencies

Units 01–05 must be complete.
The `student_detection_summary` view from Unit 03 must exist.

---

## Verify When Done

- [ ] `/history` renders the history table for a logged-in student
- [ ] Only the current student's results appear — no other students' data
- [ ] Table shows ABR, TBR, CBR, final gender, confidence, and date
- [ ] Empty state message shown when student has no history
- [ ] Pagination works for more than 10 results
- [ ] Accessing `/history` without login redirects to `/login`
- [ ] `context/progress-tracker.md` updated to mark Unit 06 complete

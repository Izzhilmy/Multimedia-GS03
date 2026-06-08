# Unit 05: Gender Detection

## Goal

Authenticated student fills in a form (name, IC number, visual features,
photo upload), submits it, and receives a result page showing all three
retrieval outputs (ABR, TBR, CBR) and the final fused gender prediction.
All data and results are saved to gs03.

---

## Design

Two pages: the submission form (`/detection`) and the result page
(`/detection/result`). The result matches the "ANALYST RESULT" UI
from the GS03 presentation slides.

---

## Implementation

### Services

#### AbrService

Attribute-Based Retrieval — derives gender from Malaysian IC number.

Malaysian IC format: `YYMMDD-PB-XXXX`
The last digit of the 4-digit sequence (position 12, 0-indexed) determines gender:
- Odd → Male
- Even → Female

Create `app/Services/AbrService.php`:

```php
<?php

namespace App\Services;

class AbrService
{
    /**
     * Derive gender from Malaysian IC number.
     * IC format: YYMMDD-PB-XXXX (12 digits, hyphens optional)
     * Last digit odd = Male, even = Female.
     */
    public function execute(string $icNumber): array
    {
        // Strip hyphens
        $digits = preg_replace('/\D/', '', $icNumber);

        if (strlen($digits) !== 12) {
            return ['prediction' => 'Unknown', 'detail' => ['ic_gender' => 'Unknown']];
        }

        $lastDigit = (int) substr($digits, -1);
        $gender = ($lastDigit % 2 !== 0) ? 'Male' : 'Female';

        return [
            'prediction' => $gender,
            'detail'     => ['ic_gender' => $gender, 'last_digit' => $lastDigit],
        ];
    }
}
```

---

#### TbrService

Text-Based Retrieval — infers gender from honorific title and name keywords.

Create `app/Services/TbrService.php`:

```php
<?php

namespace App\Services;

class TbrService
{
    private array $maleKeywords   = ['bin', 'mr', 'mr.', 'encik', 'en.', 'tuan'];
    private array $femaleKeywords = ['binti', 'bte', 'bt', 'mrs', 'mrs.', 'miss',
                                     'ms', 'ms.', 'puan', 'dr. (female)'];

    /**
     * Infer gender from full name and optional honorific.
     */
    public function execute(string $fullName, string $honorific = ''): array
    {
        $text    = strtolower(trim($honorific . ' ' . $fullName));
        $words   = preg_split('/\s+/', $text);
        $keyword = null;

        foreach ($this->femaleKeywords as $kw) {
            if (in_array($kw, $words) || str_contains($text, $kw)) {
                return [
                    'prediction' => 'Female',
                    'detail'     => ['honorific' => $honorific, 'keyword' => $kw],
                ];
            }
        }

        foreach ($this->maleKeywords as $kw) {
            if (in_array($kw, $words) || str_contains($text, $kw)) {
                $keyword = $kw;
                return [
                    'prediction' => 'Male',
                    'detail'     => ['honorific' => $honorific, 'keyword' => $kw],
                ];
            }
        }

        // No keyword found — fallback to Unknown
        return [
            'prediction' => 'Unknown',
            'detail'     => ['honorific' => $honorific, 'keyword' => null],
        ];
    }
}
```

---

#### CbrService

Content-Based Retrieval — rule-based prediction from visual feature inputs.
(The student manually selects visual features — no actual CV model.)

Rules (in priority order):
1. Hijab detected → Female
2. Facial hair present → Male
3. Hair length Long → Female
4. Hair length Short → Male
5. Hair length Medium → tie-break toward Female

Create `app/Services/CbrService.php`:

```php
<?php

namespace App\Services;

class CbrService
{
    /**
     * Predict gender from visual features entered by the student.
     *
     * @param string $hairLength    'Short' | 'Medium' | 'Long'
     * @param bool   $isHijab       hijab detected
     * @param bool   $hasFacialHair facial hair present
     */
    public function execute(string $hairLength, bool $isHijab, bool $hasFacialHair): array
    {
        $scores = ['Male' => 0, 'Female' => 0];

        if ($isHijab)       { $scores['Female'] += 3; }
        if ($hasFacialHair) { $scores['Male']   += 3; }

        match (strtolower($hairLength)) {
            'long'   => $scores['Female'] += 2,
            'short'  => $scores['Male']   += 2,
            'medium' => $scores['Female'] += 1,
            default  => null,
        };

        $prediction = $scores['Female'] >= $scores['Male'] ? 'Female' : 'Male';
        $total      = array_sum($scores);
        $confidence = $total > 0
            ? round(($scores[$prediction] / $total) * 100, 0)
            : 50;

        return [
            'prediction' => $prediction,
            'detail'     => [
                'hair_length'     => $hairLength,
                'is_hijab'        => $isHijab,
                'has_facial_hair' => $hasFacialHair,
                'confidence'      => $confidence,
            ],
        ];
    }
}
```

---

#### DetectionFusionService

Combines all three results by majority vote.

Create `app/Services/DetectionFusionService.php`:

```php
<?php

namespace App\Services;

class DetectionFusionService
{
    /**
     * Fuse ABR, TBR, CBR predictions by majority vote.
     * If a method returned 'Unknown', it is excluded from the vote.
     */
    public function execute(
        string $abrResult,
        string $tbrResult,
        string $cbrResult
    ): array {
        $votes = array_filter(
            [$abrResult, $tbrResult, $cbrResult],
            fn($v) => in_array($v, ['Male', 'Female'])
        );

        $counts = array_count_values($votes);
        $male   = $counts['Male']   ?? 0;
        $female = $counts['Female'] ?? 0;
        $total  = count($votes);

        if ($total === 0) {
            $final      = 'Unknown';
            $confidence = 0;
        } elseif ($female > $male) {
            $final      = 'Female';
            $confidence = (int) round(($female / $total) * 100);
        } else {
            $final      = 'Male';
            $confidence = (int) round(($male / $total) * 100);
        }

        return [
            'abr_result'   => $abrResult,
            'tbr_result'   => $tbrResult,
            'cbr_result'   => $cbrResult,
            'final_gender' => $final,
            'confidence'   => $confidence,
        ];
    }
}
```

---

#### DetectionResultService

Persists the analysis to gs03.

Create `app/Services/DetectionResultService.php`:

```php
<?php

namespace App\Services;

use App\Models\UserProfile;
use App\Models\IdCardInfo;
use App\Models\TextInfo;
use App\Models\ImageAnalysis;
use App\Models\DetectionResult;
use Illuminate\Http\UploadedFile;

class DetectionResultService
{
    public function execute(array $data, array $fusion): DetectionResult
    {
        // Upsert user profile (create if first time, update image/IC)
        $profile = UserProfile::updateOrCreate(
            ['matric_no' => $data['matric_no']],
            [
                'full_name'  => $data['full_name'],
                'ic_number'  => $data['ic_number'],
                'image_path' => $data['image_path'] ?? null,
            ]
        );

        // Save ABR data
        IdCardInfo::create([
            'user_profile_id' => $profile->id,
            'ic_gender'       => $data['abr']['detail']['ic_gender'],
            'abr_result'      => $fusion['abr_result'],
        ]);

        // Save TBR data
        TextInfo::create([
            'user_profile_id' => $profile->id,
            'honorific_title' => $data['tbr']['detail']['honorific'] ?? null,
            'name_keyword'    => $data['tbr']['detail']['keyword'] ?? null,
            'tbr_result'      => $fusion['tbr_result'],
        ]);

        // Save CBR data
        ImageAnalysis::create([
            'user_profile_id'  => $profile->id,
            'hair_feature'     => $data['cbr']['detail']['hair_length'],
            'is_hijab_detected'=> $data['cbr']['detail']['is_hijab'],
            'has_facial_hair'  => $data['cbr']['detail']['has_facial_hair'],
            'confidence_score' => $data['cbr']['detail']['confidence'],
            'cbr_result'       => $fusion['cbr_result'],
        ]);

        // Save final fused result
        return DetectionResult::create([
            'user_profile_id' => $profile->id,
            'abr_result'      => $fusion['abr_result'],
            'tbr_result'      => $fusion['tbr_result'],
            'cbr_result'      => $fusion['cbr_result'],
            'final_gender'    => $fusion['final_gender'],
            'confidence'      => $fusion['confidence'],
        ]);
    }
}
```

---

### Models

Create models with correct connections and tables.

`app/Models/UserProfile.php`:
```php
class UserProfile extends Model {
    protected $connection = 'mysql';
    protected $fillable   = ['matric_no','full_name','ic_number','image_path'];
}
```

`app/Models/IdCardInfo.php`:
```php
class IdCardInfo extends Model {
    protected $connection = 'mysql';
    protected $table      = 'id_card_info';
    protected $fillable   = ['user_profile_id','ic_gender','abr_result'];
}
```

`app/Models/TextInfo.php`:
```php
class TextInfo extends Model {
    protected $connection = 'mysql';
    protected $table      = 'text_info';
    protected $fillable   = ['user_profile_id','honorific_title','name_keyword','tbr_result'];
}
```

`app/Models/ImageAnalysis.php`:
```php
class ImageAnalysis extends Model {
    protected $connection = 'mysql';
    protected $table      = 'image_analysis';
    protected $fillable   = ['user_profile_id','hair_feature','is_hijab_detected',
                             'has_facial_hair','confidence_score','cbr_result'];
}
```

`app/Models/DetectionResult.php`:
```php
class DetectionResult extends Model {
    protected $connection = 'mysql';
    protected $table      = 'detection_results';
    protected $fillable   = ['user_profile_id','abr_result','tbr_result',
                             'cbr_result','final_gender','confidence'];
}
```

---

### DetectionController

Create `app/Http/Controllers/DetectionController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Services\AbrService;
use App\Services\TbrService;
use App\Services\CbrService;
use App\Services\DetectionFusionService;
use App\Services\DetectionResultService;
use Illuminate\Http\Request;

class DetectionController extends Controller
{
    public function __construct(
        private AbrService              $abrService,
        private TbrService              $tbrService,
        private CbrService              $cbrService,
        private DetectionFusionService  $fusionService,
        private DetectionResultService  $resultService,
    ) {}

    public function showForm()
    {
        return view('detection.form');
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'full_name'   => 'required|string|max:255',
            'ic_number'   => 'required|string|max:20',
            'honorific'   => 'nullable|string|max:20',
            'hair_length' => 'required|in:Short,Medium,Long',
            'is_hijab'    => 'nullable',
            'facial_hair' => 'nullable',
            'photo'       => 'nullable|image|max:2048',
        ]);

        // Handle photo upload
        $imagePath = null;
        if ($request->hasFile('photo')) {
            $imagePath = $request->file('photo')->store('uploads', 'public');
        }

        // Run all three retrievals
        $abr = $this->abrService->execute($request->input('ic_number'));
        $tbr = $this->tbrService->execute(
            $request->input('full_name'),
            $request->input('honorific', '')
        );
        $cbr = $this->cbrService->execute(
            $request->input('hair_length'),
            (bool) $request->input('is_hijab'),
            (bool) $request->input('facial_hair')
        );

        // Fuse results
        $fusion = $this->fusionService->execute(
            $abr['prediction'],
            $tbr['prediction'],
            $cbr['prediction']
        );

        // Save to gs03
        $this->resultService->execute([
            'matric_no'  => session('student.matric_no'),
            'full_name'  => $request->input('full_name'),
            'ic_number'  => $request->input('ic_number'),
            'image_path' => $imagePath,
            'abr'        => $abr,
            'tbr'        => $tbr,
            'cbr'        => $cbr,
        ], $fusion);

        // Flash result to session and redirect to result page
        session()->flash('detection_result', [
            'full_name'  => $request->input('full_name'),
            'image_path' => $imagePath,
            'abr'        => $abr,
            'tbr'        => $tbr,
            'cbr'        => $cbr,
            'fusion'     => $fusion,
        ]);

        return redirect()->route('detection.result');
    }

    public function showResult()
    {
        $result = session('detection_result');

        if (!$result) {
            return redirect()->route('detection.form')
                             ->with('error', 'No detection result found. Please submit the form.');
        }

        return view('detection.result', compact('result'));
    }
}
```

---

### Detection Form View

Create `resources/views/detection/form.blade.php`:

```blade
@extends('layouts.app')
@section('content')
<div style="background:#fff;border-radius:12px;padding:32px;box-shadow:0 2px 12px rgba(0,0,0,.1);max-width:700px;margin:0 auto">
    <h2 style="color:#1e2a4a;margin-bottom:24px;text-align:center">Gender Prediction System</h2>

    @if ($errors->any())
        <div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:6px;margin-bottom:16px">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('detection.analyze') }}" enctype="multipart/form-data">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
            <div>
                <label style="display:block;font-size:13px;color:#555;margin-bottom:4px">Upload Your Image</label>
                <input type="file" name="photo" accept="image/*"
                       style="width:100%;padding:8px;border:1px solid #ccc;border-radius:8px;margin-bottom:16px">

                <label style="display:block;font-size:13px;color:#555;margin-bottom:4px">Honorific Title</label>
                <select name="honorific" style="width:100%;padding:10px;border:1px solid #ccc;border-radius:8px;margin-bottom:16px">
                    <option value="">-- Select --</option>
                    <option value="mr">Mr.</option>
                    <option value="mrs">Mrs.</option>
                    <option value="ms">Ms.</option>
                    <option value="encik">Encik</option>
                    <option value="puan">Puan</option>
                </select>

                <label style="display:block;font-size:13px;color:#555;margin-bottom:4px">Full Name</label>
                <input type="text" name="full_name" value="{{ old('full_name') }}"
                       style="width:100%;padding:10px;border:1px solid #ccc;border-radius:8px;margin-bottom:16px">

                <label style="display:block;font-size:13px;color:#555;margin-bottom:4px">IC Number</label>
                <input type="text" name="ic_number" placeholder="e.g. 030120-14-0735"
                       value="{{ old('ic_number') }}"
                       style="width:100%;padding:10px;border:1px solid #ccc;border-radius:8px;margin-bottom:16px">
            </div>

            <div>
                <p style="font-size:13px;color:#555;margin-bottom:8px;font-weight:bold">Visual Features (for CBR)</p>

                <label style="display:block;font-size:13px;color:#555;margin-bottom:4px">Hair Length</label>
                <select name="hair_length" style="width:100%;padding:10px;border:1px solid #ccc;border-radius:8px;margin-bottom:16px">
                    <option value="Short">Short</option>
                    <option value="Medium">Medium</option>
                    <option value="Long">Long</option>
                </select>

                <label style="display:flex;align-items:center;gap:8px;font-size:14px;color:#333;margin-bottom:12px">
                    <input type="checkbox" name="is_hijab" value="1" style="width:16px;height:16px">
                    Hijab Detected
                </label>

                <label style="display:flex;align-items:center;gap:8px;font-size:14px;color:#333;margin-bottom:24px">
                    <input type="checkbox" name="facial_hair" value="1" style="width:16px;height:16px">
                    Facial Hair Present
                </label>
            </div>
        </div>

        <div style="text-align:center;margin-top:8px">
            <button type="submit"
                style="padding:12px 48px;background:#1e2a4a;color:#fff;border:none;border-radius:8px;font-size:15px;cursor:pointer">
                Analyse
            </button>
        </div>
    </form>
</div>
@endsection
```

---

### Result View

Create `resources/views/detection/result.blade.php`:

```blade
@extends('layouts.app')
@section('content')
<div style="background:#fff;border-radius:12px;padding:32px;box-shadow:0 2px 12px rgba(0,0,0,.1)">
    <h2 style="text-align:center;color:#1e2a4a;margin-bottom:24px">Analyst Result</h2>

    <div style="display:grid;grid-template-columns:120px 1fr 1fr;gap:24px;align-items:start">

        {{-- Uploaded image --}}
        <div style="text-align:center">
            <p style="font-size:12px;color:#888;margin-bottom:8px">UPLOADED IMAGE</p>
            @if($result['image_path'])
                <img src="{{ asset('storage/' . $result['image_path']) }}"
                     style="width:100px;height:100px;object-fit:cover;border-radius:50%;border:2px solid #ccc">
            @else
                <div style="width:100px;height:100px;border-radius:50%;background:#eee;display:flex;align-items:center;justify-content:center;font-size:32px">🖼️</div>
            @endif
        </div>

        {{-- CBR + TBR --}}
        <div>
            <div style="background:#a8f07a;border-radius:10px;padding:16px;margin-bottom:16px">
                <p style="font-weight:bold;margin-bottom:8px">CONTENT BASED RETRIEVAL</p>
                <p>Hijab Detected : {{ $result['cbr']['detail']['is_hijab'] ? 'Yes' : 'No' }}</p>
                <p>Hair Length : {{ $result['cbr']['detail']['hair_length'] }}</p>
                <p>Facial Hair : {{ $result['cbr']['detail']['has_facial_hair'] ? 'Yes' : 'No' }}</p>
                <p style="margin-top:8px">Prediction : <strong>{{ $result['cbr']['prediction'] }}</strong></p>
                <p>Confidence : {{ $result['cbr']['detail']['confidence'] }}%</p>
            </div>

            <div style="background:#a8f07a;border-radius:10px;padding:16px">
                <p style="font-weight:bold;margin-bottom:8px">TEXT BASED RETRIEVAL</p>
                <p>Input Text : {{ $result['full_name'] }}</p>
                <p>Detected Keyword : "{{ $result['tbr']['detail']['keyword'] ?? 'none' }}"</p>
                <p style="margin-top:8px">Prediction : <strong>{{ $result['tbr']['prediction'] }}</strong></p>
            </div>
        </div>

        {{-- ABR + Final --}}
        <div>
            <div style="background:#a8f07a;border-radius:10px;padding:16px;margin-bottom:16px">
                <p style="font-weight:bold;margin-bottom:8px">ATTRIBUTE BASED RETRIEVAL</p>
                <p>IC Gender : {{ $result['abr']['detail']['ic_gender'] }}</p>
                <p style="margin-top:8px">Prediction : <strong>{{ $result['abr']['prediction'] }}</strong></p>
            </div>

            <div style="background:#00bcd4;border-radius:10px;padding:16px;text-align:center">
                <p style="font-weight:bold;margin-bottom:8px">FINAL RESULT</p>
                <p style="font-size:20px;font-weight:bold;color:#fff">
                    {{ $result['fusion']['final_gender'] }}
                    ({{ $result['fusion']['confidence'] }}% Confidence)
                </p>
            </div>
        </div>
    </div>

    <div style="text-align:center;margin-top:24px">
        <a href="{{ route('detection.form') }}"
           style="padding:10px 32px;background:#1e2a4a;color:#fff;border-radius:8px;text-decoration:none;font-size:14px">
            New Detection
        </a>
        <a href="{{ route('history.index') }}"
           style="padding:10px 32px;background:#555;color:#fff;border-radius:8px;text-decoration:none;font-size:14px;margin-left:12px">
            View History
        </a>
    </div>
</div>
@endsection
```

---

## Dependencies

Units 01–04 must be complete.

---

## Verify When Done

- [ ] Detection form renders at `/detection` for logged-in student
- [ ] Submitting the form runs ABR, TBR, and CBR
- [ ] ABR correctly reads last digit of IC: odd → Male, even → Female
- [ ] TBR detects "bin" as Male, "binti" as Female
- [ ] CBR: hijab detected → Female; facial hair → Male
- [ ] Fusion majority vote produces correct final_gender
- [ ] 3 matching → confidence 100%; 2 vs 1 → 67%; all disagree/unknown → varies
- [ ] Result page displays all three retrieval breakdowns + final result
- [ ] Photo upload works and image renders on result page
- [ ] All 5 tables in gs03 get a new row after each submission
- [ ] Submitting without photo still works (image_path is null)
- [ ] `context/progress-tracker.md` updated to mark Unit 05 complete

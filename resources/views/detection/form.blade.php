@extends('layouts.app')
@section('content')
<style>
    .page-title { font-family:'Cinzel',serif;font-size:22px;color:var(--cream);letter-spacing:1px;margin-bottom:24px; }
    .det-card { background:var(--card-bg);border:1px solid var(--card-border);border-radius:12px;padding:28px;max-width:800px;margin:0 auto; }
    .det-grid { display:grid;grid-template-columns:1fr 1fr;gap:28px; }
    .field-label { display:block;font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--cream2);margin-bottom:6px; }
    .field-input,.field-select { width:100%;background:rgba(255,255,255,0.07);border:1px solid rgba(240,230,200,0.2);border-radius:8px;padding:11px 14px;color:var(--cream);font-size:14px;font-family:'Lato',sans-serif;margin-bottom:16px;transition:border-color 0.2s; }
    .field-input:focus,.field-select:focus { outline:none;border-color:rgba(240,230,200,0.5); }
    .field-input::placeholder { color:rgba(240,230,200,0.3); }
    .field-select option { background:var(--navy2);color:var(--cream); }
    .file-input-wrapper { width:100%;background:rgba(255,255,255,0.04);border:1.5px dashed rgba(240,230,200,0.25);border-radius:8px;padding:14px;color:var(--cream2);font-size:13px;margin-bottom:16px;cursor:pointer; }
    .file-input-wrapper input[type="file"] { width:100%;color:var(--cream2);font-family:'Lato',sans-serif;font-size:13px;background:transparent;border:none;cursor:pointer; }
    .info-preview { background:rgba(240,180,80,0.12);border:1px solid rgba(240,180,80,0.3);border-radius:10px;padding:16px;margin-bottom:20px;text-align:center; }
    .info-preview-label { font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--cream3);margin-bottom:12px; }
    .avatar-circle { width:72px;height:72px;border-radius:50%;background:rgba(255,255,255,0.08);border:1px solid rgba(240,230,200,0.2);display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto 12px;overflow:hidden; }
    .avatar-circle img { width:100%;height:100%;object-fit:cover; }
    .info-row { display:flex;justify-content:space-between;font-size:13px;color:var(--cream2);margin-bottom:4px; }
    .info-row span:first-child { color:var(--cream3);font-size:11px;text-transform:uppercase;letter-spacing:1px; }
    .cbr-box { background:rgba(0,188,212,0.07);border:1px solid rgba(0,188,212,0.25);border-radius:10px;padding:16px;margin-bottom:16px; }
    .cbr-box-title { font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--cyan);margin-bottom:10px; }
    .cbr-status { font-size:13px;color:var(--cream2);min-height:20px; }
    .cbr-result-row { display:flex;align-items:center;gap:10px;margin-top:8px; }
    .badge { display:inline-block;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:700; }
    .badge-male   { background:rgba(74,144,217,0.15);color:#7ab9f0;border:1px solid rgba(74,144,217,0.3); }
    .badge-female { background:rgba(217,74,140,0.15);color:#f07ab6;border:1px solid rgba(217,74,140,0.3); }
    .btn-enter { width:100%;background:transparent;border:1px solid rgba(240,230,200,0.3);border-radius:8px;color:var(--cream2);font-size:13px;padding:10px;cursor:pointer;font-family:'Lato',sans-serif;margin-bottom:16px;transition:background 0.15s; }
    .btn-enter:hover { background:rgba(240,230,200,0.08); }
    .btn-analyse { width:100%;background:rgba(240,230,200,0.12);border:1px solid var(--cream3);border-radius:8px;color:var(--cream);font-family:'Cinzel',serif;font-size:14px;font-weight:600;letter-spacing:1px;padding:13px;cursor:pointer;transition:background 0.2s;margin-top:8px; }
    .btn-analyse:hover { background:rgba(240,230,200,0.2); }
    .error-box { background:rgba(220,50,50,0.15);border:1px solid rgba(220,50,50,0.3);border-radius:6px;padding:10px 14px;font-size:13px;color:#ff9090;margin-bottom:20px; }
</style>

<div style="max-width:800px;margin:0 auto">
    <h2 class="page-title">GENDER PREDICTION SYSTEM</h2>
    <div class="det-card">
        @if ($errors->any())
            <div class="error-box">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('detection.analyze') }}"
              enctype="multipart/form-data" id="det-form">
            @csrf
            <input type="hidden" name="cbr_gender"     id="cbr_gender"     value="Female">
            <input type="hidden" name="cbr_confidence" id="cbr_confidence" value="50">

            <div class="det-grid">
                {{-- Left column --}}
                <div>
                    <label class="field-label">Upload Your Image</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="photo" accept="image/*" id="photo-input"
                               onchange="previewPhoto(this)">
                    </div>

                    <label class="field-label" for="honorific">Honorific Title</label>
                    <select name="honorific" id="honorific" class="field-select">
                        <option value="">-- Select --</option>
                        <option value="mr"    {{ old('honorific')=='mr'    ? 'selected':'' }}>Mr.</option>
                        <option value="mrs"   {{ old('honorific')=='mrs'   ? 'selected':'' }}>Mrs.</option>
                        <option value="ms"    {{ old('honorific')=='ms'    ? 'selected':'' }}>Ms.</option>
                        <option value="encik" {{ old('honorific')=='encik' ? 'selected':'' }}>Encik</option>
                        <option value="puan"  {{ old('honorific')=='puan'  ? 'selected':'' }}>Puan</option>
                    </select>

                    <label class="field-label" for="full_name">Enter Your Name</label>
                    <input class="field-input" type="text" id="full_name" name="full_name"
                           value="{{ old('full_name') }}" placeholder="e.g. Ahmad bin Razak" required>

                    <label class="field-label" for="ic_number">Enter Your IC Number</label>
                    <input class="field-input" type="text" id="ic_number" name="ic_number"
                           value="{{ old('ic_number') }}" placeholder="e.g. 030120-14-0735" required>

                    <button type="button" class="btn-enter" onclick="updatePreview()">Enter</button>
                </div>

                {{-- Right column --}}
                <div>
                    <div class="info-preview">
                        <div class="info-preview-label">Personal Information</div>
                        <div class="avatar-circle" id="avatar-circle">
                            <span id="avatar-placeholder">🖼</span>
                            <img id="avatar-img" src="" alt="" style="display:none">
                        </div>
                        <div class="info-row">
                            <span>Name</span><span id="preview-name">—</span>
                        </div>
                        <div class="info-row">
                            <span>IC</span><span id="preview-ic">—</span>
                        </div>
                    </div>

                    <div class="cbr-box">
                        <div class="cbr-box-title">Content Based Retrieval (Auto)</div>
                        <div class="cbr-status" id="cbr-status">Upload a photo to auto-detect gender</div>
                        <div class="cbr-result-row" id="cbr-result-row" style="display:none">
                            <span id="cbr-badge"></span>
                            <span id="cbr-conf" style="font-size:12px;color:var(--cream3)"></span>
                        </div>
                    </div>

                    <button type="submit" class="btn-analyse">ANALYSIS</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/face-api.min.js') }}"></script>
<script>
const MODEL_URL = '{{ asset('models') }}';
let modelsLoaded = false;

async function loadModels() {
    setCbrStatus('loading', 'Loading face detection model…');
    await Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
        faceapi.nets.ageGenderNet.loadFromUri(MODEL_URL),
    ]);
    modelsLoaded = true;
}

function setCbrStatus(state, text, gender, conf) {
    const statusEl = document.getElementById('cbr-status');
    const rowEl    = document.getElementById('cbr-result-row');
    statusEl.textContent = text;
    if (state === 'result') {
        rowEl.style.display = 'flex';
        const badgeClass = gender === 'Male' ? 'badge-male' : 'badge-female';
        document.getElementById('cbr-badge').innerHTML =
            `<span class="badge ${badgeClass}">${gender}</span>`;
        document.getElementById('cbr-conf').textContent = `${conf}% confidence`;
    } else {
        rowEl.style.display = 'none';
    }
}

async function analysePhoto(imgEl) {
    if (!modelsLoaded) await loadModels();
    setCbrStatus('loading', 'Analysing face…');
    const detection = await faceapi
        .detectSingleFace(imgEl, new faceapi.TinyFaceDetectorOptions())
        .withAgeAndGender();
    if (detection) {
        const gender = detection.gender === 'male' ? 'Male' : 'Female';
        const conf   = Math.round(detection.genderProbability * 100);
        document.getElementById('cbr_gender').value     = gender;
        document.getElementById('cbr_confidence').value = conf;
        setCbrStatus('result', '', gender, conf);
    } else {
        setCbrStatus('none', 'No face detected — default will be used');
    }
}

function updatePreview() {
    const name = document.getElementById('full_name').value.trim();
    const ic   = document.getElementById('ic_number').value.trim();
    document.getElementById('preview-name').textContent = name || '—';
    document.getElementById('preview-ic').textContent   = ic   || '—';
}

function previewPhoto(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById('avatar-img');
        img.src = e.target.result;
        img.style.display = 'block';
        document.getElementById('avatar-placeholder').style.display = 'none';
        img.onload = () => analysePhoto(img);
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
@endsection

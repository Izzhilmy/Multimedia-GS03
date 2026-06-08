@extends('layouts.app')
@section('content')
<style>
    .page-title {
        font-family: 'Cinzel', serif;
        font-size: 22px;
        color: var(--cream);
        letter-spacing: 1px;
        margin-bottom: 24px;
    }
    .det-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 12px;
        padding: 28px;
        max-width: 800px;
        margin: 0 auto;
    }
    .det-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 28px;
    }
    .field-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--cream2);
        margin-bottom: 6px;
    }
    .field-input, .field-select {
        width: 100%;
        background: rgba(255,255,255,0.07);
        border: 1px solid rgba(240,230,200,0.2);
        border-radius: 8px;
        padding: 11px 14px;
        color: var(--cream);
        font-size: 14px;
        font-family: 'Lato', sans-serif;
        margin-bottom: 16px;
        transition: border-color 0.2s;
    }
    .field-input:focus, .field-select:focus {
        outline: none;
        border-color: rgba(240,230,200,0.5);
    }
    .field-input::placeholder { color: rgba(240,230,200,0.3); }
    .field-select option { background: var(--navy2); color: var(--cream); }
    .file-input-wrapper {
        width: 100%;
        background: rgba(255,255,255,0.04);
        border: 1.5px dashed rgba(240,230,200,0.25);
        border-radius: 8px;
        padding: 14px;
        color: var(--cream2);
        font-size: 13px;
        margin-bottom: 16px;
        cursor: pointer;
    }
    .file-input-wrapper input[type="file"] {
        width: 100%;
        color: var(--cream2);
        font-family: 'Lato', sans-serif;
        font-size: 13px;
        background: transparent;
        border: none;
        cursor: pointer;
    }
    .info-preview {
        background: rgba(240,180,80,0.12);
        border: 1px solid rgba(240,180,80,0.3);
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 20px;
        text-align: center;
    }
    .info-preview-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--cream3);
        margin-bottom: 12px;
    }
    .avatar-circle {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(240,230,200,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin: 0 auto 12px;
        overflow: hidden;
    }
    .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }
    .info-row {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        color: var(--cream2);
        margin-bottom: 4px;
    }
    .info-row span:first-child { color: var(--cream3); font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
    .cbr-section-title {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--cream2);
        margin-bottom: 14px;
    }
    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: var(--cream2);
        margin-bottom: 12px;
        cursor: pointer;
    }
    .checkbox-label input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--cyan); }
    .btn-enter {
        width: 100%;
        background: transparent;
        border: 1px solid rgba(240,230,200,0.3);
        border-radius: 8px;
        color: var(--cream2);
        font-size: 13px;
        padding: 10px;
        cursor: pointer;
        font-family: 'Lato', sans-serif;
        margin-bottom: 16px;
        transition: background 0.15s;
    }
    .btn-enter:hover { background: rgba(240,230,200,0.08); }
    .btn-analyse {
        width: 100%;
        background: rgba(240,230,200,0.12);
        border: 1px solid var(--cream3);
        border-radius: 8px;
        color: var(--cream);
        font-family: 'Cinzel', serif;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 1px;
        padding: 13px;
        cursor: pointer;
        transition: background 0.2s;
        margin-top: 8px;
    }
    .btn-analyse:hover { background: rgba(240,230,200,0.2); }
    .error-box {
        background: rgba(220,50,50,0.15);
        border: 1px solid rgba(220,50,50,0.3);
        border-radius: 6px;
        padding: 10px 14px;
        font-size: 13px;
        color: #ff9090;
        margin-bottom: 20px;
    }
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
                            <span>Name</span>
                            <span id="preview-name">—</span>
                        </div>
                        <div class="info-row">
                            <span>IC</span>
                            <span id="preview-ic">—</span>
                        </div>
                    </div>

                    <div class="cbr-section-title">Visual Features (CBR)</div>

                    <label class="field-label" for="hair_length">Hair Length</label>
                    <select name="hair_length" id="hair_length" class="field-select">
                        <option value="Short"  {{ old('hair_length','Short')=='Short'  ? 'selected':'' }}>Short</option>
                        <option value="Medium" {{ old('hair_length')=='Medium' ? 'selected':'' }}>Medium</option>
                        <option value="Long"   {{ old('hair_length')=='Long'   ? 'selected':'' }}>Long</option>
                    </select>

                    <label class="checkbox-label">
                        <input type="checkbox" name="is_hijab" value="1"
                               {{ old('is_hijab') ? 'checked' : '' }}>
                        Hijab Detected
                    </label>

                    <label class="checkbox-label">
                        <input type="checkbox" name="facial_hair" value="1"
                               {{ old('facial_hair') ? 'checked' : '' }}>
                        Facial Hair Present
                    </label>

                    <button type="submit" class="btn-analyse">ANALYSIS</button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
function updatePreview() {
    const name = document.getElementById('full_name').value.trim();
    const ic   = document.getElementById('ic_number').value.trim();
    document.getElementById('preview-name').textContent = name || '—';
    document.getElementById('preview-ic').textContent   = ic   || '—';
}

function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('avatar-img');
            const placeholder = document.getElementById('avatar-placeholder');
            img.src = e.target.result;
            img.style.display = 'block';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection

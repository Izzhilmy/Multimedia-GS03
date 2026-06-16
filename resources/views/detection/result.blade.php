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
    .result-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 12px;
        padding: 28px;
        max-width: 900px;
        margin: 0 auto;
    }
    .result-grid {
        display: grid;
        grid-template-columns: 110px 1fr 1fr;
        gap: 20px;
        align-items: start;
    }
    .retrieval-card {
        background: rgba(125,201,110,0.2);
        border: 1px solid rgba(125,201,110,0.4);
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 16px;
    }
    .retrieval-title {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #7dc96e;
        margin-bottom: 10px;
    }
    .retrieval-row {
        font-size: 13px;
        color: var(--cream2);
        margin-bottom: 4px;
    }
    .retrieval-row strong { color: var(--cream); }
    .final-card {
        background: rgba(0,188,212,0.15);
        border: 1px solid rgba(0,188,212,0.4);
        border-radius: 10px;
        padding: 20px;
        text-align: center;
    }
    .final-title {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--cyan);
        margin-bottom: 12px;
    }
    .final-gender {
        font-family: 'Cinzel', serif;
        font-size: 22px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 6px;
    }
    .final-confidence {
        font-size: 13px;
        color: rgba(0,188,212,0.8);
    }
    .photo-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: var(--cream3);
        margin-bottom: 10px;
        text-align: center;
    }
    .avatar-circle {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: rgba(255,255,255,0.08);
        border: 2px solid rgba(240,230,200,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin: 0 auto;
        overflow: hidden;
    }
    .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }
    .badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }
    .badge-male   { background: rgba(74,144,217,0.15); color: #7ab9f0; border: 1px solid rgba(74,144,217,0.3); }
    .badge-female { background: rgba(217,74,140,0.15); color: #f07ab6; border: 1px solid rgba(217,74,140,0.3); }
    .actions { text-align: center; margin-top: 28px; display: flex; gap: 12px; justify-content: center; }
    .btn-ghost {
        background: transparent;
        border: 1px solid rgba(240,230,200,0.3);
        border-radius: 8px;
        color: var(--cream2);
        font-size: 13px;
        padding: 10px 28px;
        text-decoration: none;
        transition: background 0.15s;
    }
    .btn-ghost:hover { background: rgba(240,230,200,0.08); color: var(--cream); }
    .btn-primary {
        background: rgba(240,230,200,0.12);
        border: 1px solid var(--cream3);
        border-radius: 8px;
        color: var(--cream);
        font-family: 'Cinzel', serif;
        font-size: 13px;
        padding: 10px 28px;
        text-decoration: none;
        transition: background 0.2s;
    }
    .btn-primary:hover { background: rgba(240,230,200,0.2); }
</style>

<div style="max-width:900px;margin:0 auto">
    <h2 class="page-title">ANALYST RESULT</h2>

    <div class="result-card">
        <div class="result-grid">

            {{-- Col 1: Uploaded image --}}
            <div>
                <div class="photo-label">Uploaded Image</div>
                <div class="avatar-circle">
                    @if($result['image_path'])
                        <img src="{{ asset('storage/' . $result['image_path']) }}" alt="uploaded">
                    @else
                        🖼
                    @endif
                </div>
            </div>

            {{-- Col 2: CBR + TBR --}}
            <div>
                <div class="retrieval-card">
                    <div class="retrieval-title">Content Based Retrieval</div>
                    <div class="retrieval-row">Hijab Detected : <strong>{{ $result['cbr']['detail']['is_hijab'] ? 'Yes' : 'No' }}</strong></div>
                    <div class="retrieval-row">Hair Length : <strong>{{ $result['cbr']['detail']['hair_length'] }}</strong></div>
                    <div class="retrieval-row">Facial Hair : <strong>{{ $result['cbr']['detail']['has_facial_hair'] ? 'Yes' : 'No' }}</strong></div>
                    <div class="retrieval-row" style="margin-top:8px">
                        Prediction :
                        <span class="badge {{ strtolower($result['cbr']['prediction']) === 'male' ? 'badge-male' : 'badge-female' }}">
                            {{ $result['cbr']['prediction'] }}
                        </span>
                    </div>
                    <div class="retrieval-row">Confidence : <strong>{{ $result['cbr']['detail']['confidence'] }}%</strong></div>
                </div>

                <div class="retrieval-card">
                    <div class="retrieval-title">Text Based Retrieval</div>
                    <div class="retrieval-row">Input Text : <strong>{{ $result['full_name'] }}</strong></div>
                    <div class="retrieval-row">Keyword :
                        <strong>
                            @php
                                $kws = array_filter([
                                    $result['tbr']['detail']['title_keyword'] ?? null,
                                    $result['tbr']['detail']['name_keyword']  ?? null,
                                ]);
                            @endphp
                            {{ count($kws) ? '"' . implode('" and "', $kws) . '"' : 'none' }}
                        </strong>
                    </div>
                    <div class="retrieval-row" style="margin-top:8px">
                        Prediction :
                        <span class="badge {{ strtolower($result['tbr']['prediction']) === 'male' ? 'badge-male' : 'badge-female' }}">
                            {{ $result['tbr']['prediction'] }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Col 3: ABR + Final --}}
            <div>
                <div class="retrieval-card">
                    <div class="retrieval-title">Attribute Based Retrieval</div>
                    <div class="retrieval-row">IC Gender : <strong>{{ $result['abr']['detail']['ic_gender'] }}</strong></div>
                    <div class="retrieval-row" style="margin-top:8px">
                        Prediction :
                        <span class="badge {{ strtolower($result['abr']['prediction']) === 'male' ? 'badge-male' : 'badge-female' }}">
                            {{ $result['abr']['prediction'] }}
                        </span>
                    </div>
                </div>

                <div class="final-card">
                    <div class="final-title">Final Result</div>
                    <div class="final-gender">{{ $result['fusion']['final_gender'] }}</div>
                    <div class="final-confidence">({{ $result['fusion']['confidence'] }}% Confidence)</div>
                </div>
            </div>

        </div>

        <div class="actions">
            <a href="{{ route('detection.form') }}" class="btn-primary">New Detection</a>
            <a href="{{ route('history.index') }}" class="btn-ghost">View History</a>
        </div>
    </div>
</div>
@endsection

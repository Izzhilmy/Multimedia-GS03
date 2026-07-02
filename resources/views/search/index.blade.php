@extends('layouts.app')
@section('content')
<style>
    .page-title { font-family: 'Cinzel', serif; font-size: 22px; color: var(--cream); letter-spacing: 1px; margin-bottom: 24px; }
    .search-card { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 12px; padding: 28px; }
    .search-row { display: flex; gap: 12px; margin-bottom: 10px; }
    .search-input {
        flex: 1; background: rgba(255,255,255,0.06); border: 1px solid rgba(240,230,200,0.2);
        border-radius: 8px; color: var(--cream); font-family: 'Lato', sans-serif;
        font-size: 14px; padding: 10px 16px; outline: none;
    }
    .search-input::placeholder { color: rgba(240,230,200,0.35); }
    .search-input:focus { border-color: var(--cyan); }
    .btn-search {
        background: rgba(0,188,212,0.15); border: 1px solid var(--cyan); border-radius: 8px;
        color: var(--cyan); font-family: 'Cinzel', serif; font-size: 13px;
        padding: 10px 24px; cursor: pointer; transition: background 0.2s; white-space: nowrap;
    }
    .btn-search:hover { background: rgba(0,188,212,0.28); }
    .hint { font-size: 12px; color: rgba(240,230,200,0.38); margin-bottom: 24px; }
    .result-count { font-size: 12px; color: var(--cream3); margin-bottom: 16px; }
    .cards-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
    .person-card {
        background: rgba(255,255,255,0.05); border: 1px solid rgba(240,230,200,0.14);
        border-radius: 10px; padding: 20px 16px; text-align: center;
        display: flex; flex-direction: column; align-items: center; gap: 10px;
        transition: border-color 0.15s;
    }
    .person-card:hover { border-color: rgba(240,230,200,0.3); }
    .card-photo {
        width: 80px; height: 80px; border-radius: 50%;
        background: rgba(255,255,255,0.08); border: 2px solid rgba(240,230,200,0.2);
        display: flex; align-items: center; justify-content: center;
        font-size: 28px; overflow: hidden; flex-shrink: 0;
    }
    .card-photo img { width: 100%; height: 100%; object-fit: cover; }
    .card-name { font-size: 13px; color: var(--cream); font-weight: 700; line-height: 1.3; }
    .card-date { font-size: 11px; color: var(--cream3); }
    .badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .badge-male   { background: rgba(74,144,217,0.15); color: #7ab9f0; border: 1px solid rgba(74,144,217,0.3); }
    .badge-female { background: rgba(217,74,140,0.15); color: #f07ab6; border: 1px solid rgba(217,74,140,0.3); }
    .btn-details {
        margin-top: 4px; background: rgba(240,230,200,0.08); border: 1px solid rgba(240,230,200,0.25);
        border-radius: 6px; color: var(--cream2); font-size: 12px; padding: 6px 18px;
        cursor: pointer; transition: background 0.15s; font-family: 'Lato', sans-serif;
    }
    .btn-details:hover { background: rgba(240,230,200,0.15); color: var(--cream); }
    .empty-state { text-align: center; padding: 48px 0; color: rgba(240,230,200,0.3); font-size: 14px; }

    /* Modal */
    #modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.6); z-index: 200; align-items: center; justify-content: center;
    }
    #modal-overlay.active { display: flex; }
    #modal-box {
        background: #1e2d52; border: 1px solid rgba(240,230,200,0.2); border-radius: 14px;
        padding: 28px; width: 380px; max-width: 90vw; position: relative;
    }
    #modal-close {
        position: absolute; top: 14px; right: 16px; background: none; border: none;
        color: var(--cream3); font-size: 18px; cursor: pointer; line-height: 1;
    }
    .modal-photo {
        width: 90px; height: 90px; border-radius: 50%; margin: 0 auto 16px;
        background: rgba(255,255,255,0.08); border: 2px solid rgba(240,230,200,0.2);
        display: flex; align-items: center; justify-content: center;
        font-size: 32px; overflow: hidden;
    }
    .modal-photo img { width: 100%; height: 100%; object-fit: cover; }
    .modal-name { font-family: 'Cinzel', serif; font-size: 15px; color: var(--cream); text-align: center; margin-bottom: 4px; }
    .modal-ic   { font-size: 12px; color: var(--cream3); text-align: center; margin-bottom: 16px; }
    .modal-row  { display: flex; justify-content: space-between; font-size: 13px; color: var(--cream2); padding: 6px 0; border-bottom: 1px solid rgba(240,230,200,0.07); }
    .modal-row:last-child { border-bottom: none; }
    .modal-label { color: var(--cream3); }
    .modal-final { text-align: center; margin-top: 16px; padding: 14px; background: rgba(0,188,212,0.1); border: 1px solid rgba(0,188,212,0.3); border-radius: 8px; }
    .modal-final-gender { font-family: 'Cinzel', serif; font-size: 18px; color: #fff; }
    .modal-final-conf   { font-size: 12px; color: rgba(0,188,212,0.8); margin-top: 4px; }
</style>

<div style="max-width:900px;margin:0 auto">
    <h2 class="page-title">PERSON SEARCH</h2>
    <div class="search-card">
        <form method="GET" action="{{ route('search.index') }}">
            <div class="search-row">
                <input type="text" name="q" class="search-input"
                    placeholder="Describe a person..."
                    value="{{ $description }}" autofocus autocomplete="off">
                <button type="submit" class="btn-search">Search</button>
            </div>
        </form>
        <p class="hint">Try: "female with hijab", "male short hair no beard", "female binti long hair", "lelaki janggut"</p>

        @if($results !== null)
            @if($results->isEmpty())
                <div class="empty-state">No records match your description.</div>
            @else
                <div class="result-count">{{ $results->total() }} record(s) found</div>
                <div class="cards-grid">
                    @foreach($results as $row)
                    <div class="person-card" data-row="{{ json_encode($row) }}">
                        <div class="card-photo">
                            @if($row->image_path)
                                <img src="{{ asset('storage/' . $row->image_path) }}" alt="">
                            @else
                                🖼
                            @endif
                        </div>
                        <div class="card-name">{{ $row->full_name }}</div>
                        <span class="badge {{ strtolower($row->final_gender) === 'male' ? 'badge-male' : 'badge-female' }}">
                            {{ $row->final_gender }}
                        </span>
                        <div class="card-date">{{ \Carbon\Carbon::parse($row->detected_at)->format('d M Y') }}</div>
                        <button class="btn-details" onclick="showModal(this)">Details</button>
                    </div>
                    @endforeach
                </div>
                <div style="margin-top:20px">{{ $results->appends(['q' => $description])->links() }}</div>
            @endif
        @endif
    </div>
</div>

<div id="modal-overlay" onclick="closeModal(event)">
    <div id="modal-box">
        <button id="modal-close" onclick="document.getElementById('modal-overlay').classList.remove('active')">✕</button>
        <div id="modal-photo" class="modal-photo"></div>
        <div id="modal-name" class="modal-name"></div>
        <div id="modal-ic"   class="modal-ic"></div>
        <div id="modal-rows"></div>
        <div id="modal-final" class="modal-final">
            <div id="modal-gender" class="modal-final-gender"></div>
            <div id="modal-conf"   class="modal-final-conf"></div>
        </div>
    </div>
</div>

<script>
function showModal(btn) {
    const r = JSON.parse(btn.closest('.person-card').dataset.row);
    const img = r.image_path
        ? `<img src="/storage/${r.image_path}" alt="">`
        : '🖼';
    document.getElementById('modal-photo').innerHTML  = img;
    document.getElementById('modal-name').textContent = r.full_name;
    document.getElementById('modal-ic').textContent   = r.ic_number;
    document.getElementById('modal-rows').innerHTML   =
        row('ABR Result', r.abr_result) +
        row('TBR Result', r.tbr_result) +
        row('CBR Result', r.cbr_result) +
        row('Detected On', r.detected_at ? r.detected_at.substring(0, 10) : '—');
    document.getElementById('modal-gender').textContent = r.final_gender;
    document.getElementById('modal-conf').textContent   = r.confidence + '% Confidence';
    document.getElementById('modal-overlay').classList.add('active');
}
function row(label, value) {
    return `<div class="modal-row"><span class="modal-label">${label}</span><span>${value ?? '—'}</span></div>`;
}
function closeModal(e) {
    if (e.target.id === 'modal-overlay') document.getElementById('modal-overlay').classList.remove('active');
}
</script>
@endsection

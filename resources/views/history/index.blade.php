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
    .history-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 12px;
        overflow: hidden;
        max-width: 950px;
    }
    .history-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .history-table thead tr {
        background: rgba(15,20,40,0.8);
    }
    .history-table th {
        padding: 12px 14px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--cream3);
        text-align: left;
        white-space: nowrap;
    }
    .history-table td {
        padding: 12px 14px;
        border-bottom: 1px solid rgba(240,230,200,0.07);
        color: var(--cream2);
        vertical-align: middle;
    }
    .history-table tbody tr:hover td {
        background: rgba(255,255,255,0.03);
    }
    .history-table tbody tr:last-child td {
        border-bottom: none;
    }
    .badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }
    .badge-male   { background: rgba(74,144,217,0.15); color: #7ab9f0; border: 1px solid rgba(74,144,217,0.3); }
    .badge-female { background: rgba(217,74,140,0.15); color: #f07ab6; border: 1px solid rgba(217,74,140,0.3); }
    .confidence-cell {
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }
    .conf-track {
        width: 60px;
        height: 4px;
        background: rgba(240,230,200,0.1);
        border-radius: 2px;
        overflow: hidden;
        flex-shrink: 0;
    }
    .conf-fill {
        height: 100%;
        background: var(--cyan);
        border-radius: 2px;
    }
    .conf-text {
        font-size: 12px;
        color: var(--cream3);
    }
    .empty-state {
        padding: 48px 24px;
        text-align: center;
        color: var(--cream3);
        font-size: 14px;
    }
    .empty-state a {
        color: var(--cyan);
        text-decoration: none;
        margin-left: 4px;
    }
    .empty-state a:hover { text-decoration: underline; }
    .pagination-wrap {
        padding: 16px 20px;
        border-top: 1px solid rgba(240,230,200,0.07);
    }
    /* Override Laravel pagination for dark theme */
    .pagination-wrap nav span, .pagination-wrap nav a {
        color: var(--cream2) !important;
        background: transparent !important;
        border-color: rgba(240,230,200,0.15) !important;
        font-size: 13px;
    }
    .pagination-wrap nav a:hover { background: rgba(240,230,200,0.08) !important; }
</style>

<div style="max-width:950px">
    <h2 class="page-title">DETECTION HISTORY</h2>

    <div class="history-card">
        @if($results->isEmpty())
            <div class="empty-state">
                No detection results yet.
                <a href="{{ route('detection.form') }}">Run your first detection →</a>
            </div>
        @else
            <table class="history-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>IC</th>
                        <th>ABR</th>
                        <th>TBR</th>
                        <th>CBR</th>
                        <th>Final</th>
                        <th>Confidence</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $i => $row)
                    <tr>
                        <td style="color:var(--cream3)">{{ $results->firstItem() + $i }}</td>
                        <td style="color:var(--cream)">{{ $row->full_name }}</td>
                        <td>{{ $row->ic_number ?? '—' }}</td>
                        <td>
                            <span class="badge {{ strtolower($row->abr_result) === 'male' ? 'badge-male' : 'badge-female' }}">
                                {{ $row->abr_result }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ strtolower($row->tbr_result) === 'male' ? 'badge-male' : 'badge-female' }}">
                                {{ $row->tbr_result }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ strtolower($row->cbr_result) === 'male' ? 'badge-male' : 'badge-female' }}">
                                {{ $row->cbr_result }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ strtolower($row->final_gender) === 'male' ? 'badge-male' : 'badge-female' }}"
                                  style="font-size:12px;padding:3px 12px">
                                {{ $row->final_gender }}
                            </span>
                        </td>
                        <td>
                            <div class="confidence-cell">
                                <div class="conf-track">
                                    <div class="conf-fill" style="width:{{ $row->confidence }}%"></div>
                                </div>
                                <span class="conf-text">{{ $row->confidence }}%</span>
                            </div>
                        </td>
                        <td style="color:var(--cream3);font-size:12px">
                            {{ \Carbon\Carbon::parse($row->detected_at)->format('d M Y, H:i') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($results->hasPages())
                <div class="pagination-wrap">
                    {{ $results->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

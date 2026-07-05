@extends('layouts.app')

@section('content')
<style>
    .profile-title {
        font-family: 'Cinzel', serif;
        font-size: 20px;
        color: var(--cream);
        letter-spacing: 1px;
        margin-bottom: 20px;
    }

    .profile-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 12px;
        overflow: hidden;
    }

    .profile-table {
        width: 100%;
        border-collapse: collapse;
    }

    .profile-table tr:not(:last-child) {
        border-bottom: 1px solid rgba(240,230,200,0.07);
    }

    .profile-table tr:hover {
        background: rgba(255,255,255,0.03);
    }

    .profile-table td {
        padding: 14px 20px;
        font-size: 14px;
        vertical-align: top;
    }

    .profile-label {
        color: var(--cream3);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        width: 160px;
        white-space: nowrap;
    }

    .profile-value {
        color: var(--cream);
    }

    .profile-value.muted {
        color: var(--cream3);
        font-style: italic;
    }
</style>

<h1 class="profile-title">STUDENT PROFILE</h1>

@if (!$student)
    <div class="flash-error">Unable to load profile data.</div>
@else
<div class="profile-card">
    <table class="profile-table">
        <tr>
            <td class="profile-label">Full Name</td>
            <td class="profile-value">{{ $student->full_name ?? '—' }}</td>
        </tr>
        <tr>
            <td class="profile-label">Matric No.</td>
            <td class="profile-value">{{ $student->matric_no ?? '—' }}</td>
        </tr>
        <tr>
            <td class="profile-label">Phone No.</td>
            <td class="profile-value">{{ $student->phone_no ?? '—' }}</td>
        </tr>
        <tr>
            <td class="profile-label">Group</td>
            <td class="profile-value">{{ $student->group_no ?? '—' }}</td>
        </tr>
        <tr>
            <td class="profile-label">Life Motto</td>
            <td class="profile-value">{{ $student->life_motto ?? '—' }}</td>
        </tr>
        <tr>
            <td class="profile-label">Photo File</td>
            <td class="profile-value {{ $student->photoStu ? '' : 'muted' }}">
                {{ $student->photoStu ?? 'Not uploaded' }}
            </td>
        </tr>
        <tr>
            <td class="profile-label">Photo Date</td>
            <td class="profile-value {{ $student->photoStu_date ? '' : 'muted' }}">
                {{ $student->photoStu_date ?? '—' }}
            </td>
        </tr>
        <tr>
            <td class="profile-label">Document File</td>
            <td class="profile-value {{ $student->docStu ? '' : 'muted' }}">
                {{ $student->docStu ?? 'Not uploaded' }}
            </td>
        </tr>
        <tr>
            <td class="profile-label">Document Date</td>
            <td class="profile-value {{ $student->docStu_date ? '' : 'muted' }}">
                {{ $student->docStu_date ?? '—' }}
            </td>
        </tr>
    </table>
</div>
@endif
@endsection

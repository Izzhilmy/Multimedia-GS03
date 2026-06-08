<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Gender Detection System</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy:        #1a2744;
            --navy2:       #243358;
            --navy3:       #2d3f6b;
            --cream:       #f0e6c8;
            --cream2:      #e8d9af;
            --cream3:      #d4c08a;
            --card-bg:     rgba(255,255,255,0.06);
            --card-border: rgba(240,230,200,0.18);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--navy);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Lato', sans-serif;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(ellipse at 50% 0%, rgba(45,63,107,0.6), transparent 70%);
            pointer-events: none;
        }

        .deco-border {
            position: relative;
            padding: 40px 36px;
            border: 1.5px solid rgba(240,230,200,0.25);
            border-radius: 4px;
            width: 380px;
            text-align: center;
        }

        .deco-border::before,
        .deco-border::after {
            content: '✦';
            position: absolute;
            color: var(--cream3);
            font-size: 14px;
            top: -10px;
        }
        .deco-border::before { left: 16px; }
        .deco-border::after  { right: 16px; }

        .brand-title {
            font-family: 'Cinzel', serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--cream);
            letter-spacing: 2px;
            line-height: 1.2;
        }

        .brand-sub {
            font-family: 'Cinzel', serif;
            font-size: 12px;
            color: var(--cream3);
            letter-spacing: 4px;
            margin-top: 4px;
            margin-bottom: 28px;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 24px 20px;
            text-align: left;
        }

        .error-box {
            background: rgba(220,50,50,0.15);
            border: 1px solid rgba(220,50,50,0.3);
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 13px;
            color: #ff9090;
            margin-bottom: 16px;
        }

        .field-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--cream2);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .field-input {
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

        .field-input:focus {
            outline: none;
            border-color: rgba(240,230,200,0.5);
        }

        .field-input::placeholder { color: rgba(240,230,200,0.3); }

        .btn-login {
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
            margin-top: 4px;
        }

        .btn-login:hover { background: rgba(240,230,200,0.2); }

        .footer-text {
            font-size: 11px;
            color: rgba(212,192,138,0.5);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 24px;
        }
    </style>
</head>
<body>
    <div class="deco-border">
        <div class="brand-title">GENDER DETECTION</div>
        <div class="brand-title">SYSTEM</div>
        <div class="brand-sub">GS03</div>

        <div class="card">
            @if ($errors->any())
                <div class="error-box">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <label class="field-label" for="matric_no">Matric Number</label>
                <input class="field-input" type="text" id="matric_no" name="matric_no"
                       value="{{ old('matric_no') }}" placeholder="e.g. B032420099" required autofocus>

                <label class="field-label" for="password">Password</label>
                <input class="field-input" type="password" id="password" name="password"
                       placeholder="••••••••" required>

                <button type="submit" class="btn-login">LOG IN</button>
            </form>
        </div>

        <div class="footer-text">GS03 · Multimedia Database Systems</div>
    </div>
</body>
</html>

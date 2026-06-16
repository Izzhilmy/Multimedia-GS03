<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gender Detection System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
            --cyan:        #00bcd4;
            --male:        #4a90d9;
            --female:      #d94a8c;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--navy);
            min-height: 100vh;
            font-family: 'Lato', sans-serif;
            color: var(--cream);
        }

        /* Navbar */
        nav {
            background: rgba(15,20,40,0.95);
            border-bottom: 1px solid rgba(240,230,200,0.18);
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 52px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-brand {
            font-family: 'Cinzel', serif;
            font-size: 15px;
            color: var(--cream);
            letter-spacing: 1px;
            text-decoration: none;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .nav-matric {
            font-size: 12px;
            color: var(--cream3);
            margin-right: 8px;
        }

        .nav-link {
            font-size: 13px;
            color: var(--cream2);
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            transition: background 0.15s;
        }

        .nav-link:hover,
        .nav-link.active { background: rgba(240,230,200,0.1); color: var(--cream); }

        .nav-logout {
            font-size: 13px;
            color: var(--cream2);
            background: transparent;
            border: 1px solid rgba(240,230,200,0.3);
            border-radius: 6px;
            padding: 5px 14px;
            cursor: pointer;
            font-family: 'Lato', sans-serif;
            margin-left: 4px;
            transition: all 0.15s;
        }

        .nav-logout:hover { background: rgba(240,230,200,0.1); color: var(--cream); }

        /* Container */
        .container {
            max-width: 950px;
            margin: 32px auto;
            padding: 0 16px;
        }

        /* Flash messages */
        .flash-success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 16px;
        }

        .flash-error {
            background: rgba(220,50,50,0.15);
            border: 1px solid rgba(220,50,50,0.3);
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 13px;
            color: #ff9090;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <nav>
        <a class="nav-brand" href="{{ route('detection.form') }}">GENDER DETECTION SYSTEM</a>
        <div class="nav-right">
            <span class="nav-matric">{{ session('student.matric_no') }}</span>
            <a class="nav-link {{ request()->routeIs('detection.*') ? 'active' : '' }}"
               href="{{ route('detection.form') }}">Detection</a>
            <a class="nav-link {{ request()->routeIs('history.*') ? 'active' : '' }}"
               href="{{ route('history.index') }}">History</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-logout">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="flash-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</body>
</html>

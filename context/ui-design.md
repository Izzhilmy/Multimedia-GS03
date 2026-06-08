# UI Design — Gender Detection System (GS03)

## Visual Identity

The design mirrors the GS03 presentation slides:
dark navy as the dominant background, cream/beige typography,
decorative ornamental borders on key containers, and the
`Cinzel` serif for headings with `Lato` for body text.

---

## Design Tokens

### Colors

| Token | Hex | Usage |
|---|---|---|
| `--navy` | `#1a2744` | Page background |
| `--navy2` | `#243358` | Card/panel background |
| `--navy3` | `#2d3f6b` | Hover states, button fill |
| `--cream` | `#f0e6c8` | Primary text, headings |
| `--cream2` | `#e8d9af` | Secondary text, labels |
| `--cream3` | `#d4c08a` | Muted text, borders |
| `--card-bg` | `rgba(255,255,255,0.06)` | Card surface |
| `--card-border` | `rgba(240,230,200,0.18)` | Card border |
| `--green-cbr` | `rgba(125,201,110,0.2)` | CBR retrieval card background |
| `--green-cbr-border` | `rgba(125,201,110,0.4)` | CBR retrieval card border |
| `--cyan` | `#00bcd4` | Final result card accent |
| `--male` | `#4a90d9` | Male gender badge |
| `--female` | `#d94a8c` | Female gender badge |

### Typography

```css
/* Headings — decorative, matches slide aesthetic */
font-family: 'Cinzel', serif;

/* Body — all labels, inputs, table content */
font-family: 'Lato', sans-serif;
```

Import via Google Fonts:
```html
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
```

### Borders & Radius

- Card borders: `1px solid rgba(240,230,200,0.18)`
- Card radius: `12px`
- Input radius: `8px`
- Button radius: `8px`
- Decorative ornamental border (login page): `1.5px solid rgba(240,230,200,0.25)` with `border-radius: 4px`

---

## Pages

### 1. Login Page

**Route:** `GET /login`

**Layout:**
- Full viewport height, centered vertically and horizontally
- Radial gradient overlay: `radial-gradient(ellipse at 50% 0%, rgba(45,63,107,0.6), transparent 70%)`
- Decorative ornamental border wraps the entire login card container
- No navbar

**Components:**

```
┌─ Decorative border (deco-border) ──────────────────────────────┐
│                                                                  │
│     GENDER DETECTION          ← Cinzel 28px, cream, centered    │
│          SYSTEM               ← Cinzel 12px, cream3, spaced     │
│                                                                  │
│   ┌─ Card (card-bg, card-border, radius 12px) ───────────────┐  │
│   │  [Error message — red bg, only shown on failure]         │  │
│   │                                                           │  │
│   │  MATRIC NUMBER            ← uppercase label, 12px        │  │
│   │  [________________________]  ← input, cream text         │  │
│   │                                                           │  │
│   │  PASSWORD                                                 │  │
│   │  [________________________]                               │  │
│   │                                                           │  │
│   │  [        LOG IN         ]  ← Cinzel, full-width btn     │  │
│   └───────────────────────────────────────────────────────────┘  │
│                                                                  │
│     GS03 · MULTIMEDIA DATABASE SYSTEMS  ← tiny muted footer     │
└──────────────────────────────────────────────────────────────────┘
```

**Behaviour:**
- Correct credentials → session set → redirect to `/detection`
- Wrong credentials → error message shown inline, form stays on page
- If already logged in, redirect to `/detection`

---

### 2. Detection Form Page

**Route:** `GET /detection`

**Layout:**
- Navbar present (see Navbar section)
- Max width `800px`, centered
- Page title: `GENDER PREDICTION SYSTEM` (Cinzel, 22px)
- Single card containing a 2-column grid

**Left column — inputs:**

```
UPLOAD YOUR IMAGE
[ 📁  Choose file… ]   ← dashed border file input

HONORIFIC TITLE
[ Mr. / Mrs. / Ms. / Encik / Puan ▾ ]

ENTER YOUR NAME
[_____________________________]

ENTER YOUR IC NUMBER
[_____________________________]  e.g. 030120-14-0735
```

**Right column — personal info preview + CBR features:**

```
┌─ Personal Information (amber tint card) ─────────────────┐
│  PERSONAL INFORMATION         ← 11px uppercase label     │
│                                                           │
│          [ photo avatar / 🖼 ]   ← 72px circle           │
│                                                           │
│  NAME  :  [live preview]                                  │
│  IC    :  [live preview]                                  │
└───────────────────────────────────────────────────────────┘

VISUAL FEATURES (CBR)

HAIR LENGTH
[ Short / Medium / Long ▾ ]

☐ Hijab Detected
☐ Facial Hair Present
```

**Personal info preview card:**
- Background: `rgba(240,180,80,0.12)`
- Border: `1px solid rgba(240,180,80,0.3)`
- Updates live when "Enter" button is clicked

**"Enter" button:** transparent, cream border — updates personal info preview only

**"Analysis" button:** Cinzel font, navy glass fill, cream border — triggers detection

---

### 3. Analyst Result Page

**Route:** `GET /detection/result`

**Layout:**
- Navbar present
- Max width `900px`, centered
- Page title: `ANALYST RESULT` (Cinzel, 22px)
- Single card containing a 3-column grid

**3-column grid:**

```
┌── Col 1 (110px) ──┬──────── Col 2 ────────┬──────── Col 3 ────────┐
│  UPLOADED IMAGE   │  CONTENT BASED         │  ATTRIBUTE BASED      │
│                   │  RETRIEVAL             │  RETRIEVAL            │
│  [ photo/🖼 ]     │  ┌──────────────────┐  │  ┌──────────────────┐ │
│  90px circle      │  │ Hijab Detected:  │  │  │ IC Gender: Male  │ │
│                   │  │ Hair Length:     │  │  │                  │ │
│                   │  │ Facial Hair:     │  │  │ Prediction: Male │ │
│                   │  │ Prediction: Male │  │  └──────────────────┘ │
│                   │  │ Confidence: 87%  │  │                       │
│                   │  └──────────────────┘  │  ┌──────────────────┐ │
│                   │                        │  │   FINAL RESULT   │ │
│                   │  TEXT BASED RETRIEVAL  │  │                  │ │
│                   │  ┌──────────────────┐  │  │  Male            │ │
│                   │  │ Input Text:      │  │  │  (92% Confidence)│ │
│                   │  │ Keyword: "bin"   │  │  └──────────────────┘ │
│                   │  │ Prediction: Male │  │                       │
│                   │  └──────────────────┘  │                       │
└───────────────────┴────────────────────────┴───────────────────────┘

               [ New Detection ]    [ View History ]
```

**Retrieval cards (CBR, TBR, ABR):**
- Background: `rgba(125,201,110,0.2)` (green tint)
- Border: `1px solid rgba(125,201,110,0.4)`
- Title: `#7dc96e`, 12px uppercase, bold

**Final result card:**
- Background: `rgba(0,188,212,0.15)` (cyan tint)
- Border: `1px solid rgba(0,188,212,0.4)`
- Value: Cinzel 20px bold, white

---

### 4. History Page

**Route:** `GET /history`

**Layout:**
- Navbar present
- Max width `950px`, left-aligned title
- Card with overflow hidden wrapping the table (no padding — table fills the card edge to edge)

**Table columns:**
`#` | `Name` | `IC` | `ABR` | `TBR` | `CBR` | `Final` | `Confidence` | `Date`

**Table styles:**
- Header: `rgba(15,20,40,0.8)` background, cream3 uppercase 11px labels
- Row hover: `rgba(255,255,255,0.03)` background
- Row divider: `1px solid rgba(240,230,200,0.07)`
- Gender badges: pill-shaped with male (blue tint) or female (pink tint) color

**Gender badge styles:**

```css
/* Male badge */
background: rgba(74,144,217,0.15);
color: #7ab9f0;
border: 1px solid rgba(74,144,217,0.3);

/* Female badge */
background: rgba(217,74,140,0.15);
color: #f07ab6;
border: 1px solid rgba(217,74,140,0.3);
```

**Confidence column:** small horizontal bar + percentage text side by side
- Track: `rgba(240,230,200,0.1)`, height 4px
- Fill: `#00bcd4` (cyan), width = confidence %

**Empty state:**
```
No detection results yet.
[Run your first detection →]   ← link to /detection
```

**Pagination:** Laravel default `$results->links()` — paginate at 10 per page

---

## Navbar (all protected pages)

```
┌──────────────────────────────────────────────────────────────────┐
│ GENDER DETECTION SYSTEM      [matric_no]  Detection  History  [Logout] │
└──────────────────────────────────────────────────────────────────┘
```

- Background: `rgba(15,20,40,0.95)`
- Bottom border: `1px solid rgba(240,230,200,0.18)`
- Brand: Cinzel 15px
- Nav links: 13px, rounded 6px, hover bg at 10% cream
- Active link: slightly brighter bg
- Logout: outlined button (1px border)
- Student matric_no shown as small muted label before the links

---

## Shared Components

### Form Input

```css
background: rgba(255,255,255,0.07);
border: 1px solid rgba(240,230,200,0.2);
border-radius: 8px;
padding: 11px 14px;
color: var(--cream);
font-size: 14px;
transition: border-color 0.2s;

/* focus */
border-color: rgba(240,230,200,0.5);
```

### Primary Button (Log In / Analysis)

```css
background: rgba(240,230,200,0.12);
border: 1px solid var(--cream3);
border-radius: 8px;
color: var(--cream);
font-family: 'Cinzel', serif;
font-size: 14px;
letter-spacing: 1px;
padding: 13px;
transition: all 0.2s;

/* hover */
background: rgba(240,230,200,0.2);
```

### Ghost Button (secondary actions)

```css
background: transparent;
border: 1px solid rgba(240,230,200,0.3);
border-radius: 8px;
color: var(--cream2);
font-size: 13px;
padding: 10px 28px;
```

### Flash Messages

```html
<!-- Success -->
<div style="background:#d4edda;color:#155724;padding:10px;border-radius:6px;margin-bottom:16px">
    {{ session('success') }}
</div>

<!-- Error -->
<div style="background:rgba(220,50,50,0.15);border:1px solid rgba(220,50,50,0.3);
     border-radius:6px;padding:10px 14px;font-size:13px;color:#ff9090;">
    {{ session('error') }}
</div>
```

---

## Blade Layout Skeleton

`resources/views/layouts/app.blade.php` must include:

1. Google Fonts import (Cinzel + Lato)
2. CSS variables defined on `:root`
3. Navy background on `body`
4. Navbar with student name from `session('student.full_name')` or `session('student.matric_no')`
5. Flash message area
6. `@yield('content')` wrapped in `.container` div (max-width 950px, margin auto)

---

## File Checklist

```
resources/views/
├── layouts/
│   └── app.blade.php          ← navbar, flash messages, @yield('content')
├── auth/
│   └── login.blade.php        ← standalone page, no @extends
├── detection/
│   ├── form.blade.php         ← @extends layouts.app
│   └── result.blade.php       ← @extends layouts.app
└── history/
    └── index.blade.php        ← @extends layouts.app
```

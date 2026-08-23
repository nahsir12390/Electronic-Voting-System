<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ElectraVote · secure e-voting</title>
  <!-- Tailwind via CDN + custom solid layer -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* additional solid refinements – deeper shadows, sharper contrast, robust tokens */
    * { box-sizing: border-box; margin: 0; }
    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background: #f0f6f4;
    }
    .glass-panel {
      background: rgba(255,255,255,0.68);
      backdrop-filter: blur(4px);
      -webkit-backdrop-filter: blur(4px);
      border: 1px solid rgba(255,255,255,0.5);
    }
    .solid-shadow {
      box-shadow: 0 20px 40px -12px rgba(0,20,16,0.35), 0 8px 24px -6px rgba(0,0,0,0.08);
    }
    .hero-grid-bg {
      background-image: 
        radial-gradient(circle at 10% 20%, rgba(16,185,129,0.08) 0%, transparent 50%),
        radial-gradient(circle at 90% 70%, rgba(20,184,166,0.06) 0%, transparent 50%),
        linear-gradient(145deg, #eaf4f0 0%, #f2faf7 100%);
    }
    .candidate-card {
      transition: all 0.15s ease;
      border: 1px solid rgba(30,41,59,0.08);
      background: rgba(255,255,255,0.75);
      backdrop-filter: blur(2px);
    }
    .candidate-card:hover {
      background: white;
      border-color: rgba(16,185,129,0.25);
      box-shadow: 0 8px 18px -8px rgba(16,185,129,0.18);
    }
    .badge-open {
      background: #10b981;
      color: #0b2e26;
      font-weight: 700;
      letter-spacing: 0.02em;
      box-shadow: 0 2px 6px rgba(16,185,129,0.3);
    }
    .btn-primary-solid {
      background: #0b3b32;
      color: white;
      border: 1px solid #1e5a4a;
      box-shadow: 0 8px 20px -8px rgba(11,59,50,0.4);
      transition: 0.15s;
    }
    .btn-primary-solid:hover {
      background: #0f4d41;
      transform: scale(1.01);
      box-shadow: 0 12px 28px -10px #0b3b32;
    }
    .btn-outline-solid {
      background: rgba(255,255,255,0.5);
      backdrop-filter: blur(4px);
      border: 1px solid #1e293b;
      color: #1e293b;
      font-weight: 600;
      transition: 0.15s;
    }
    .btn-outline-solid:hover {
      background: white;
      border-color: #0b3b32;
      color: #0b3b32;
    }
    .stat-bar {
      background: #e6edeb;
      border-radius: 40px;
      overflow: hidden;
      height: 8px;
    }
    .stat-fill {
      height: 100%;
      border-radius: 40px;
      background: linear-gradient(90deg, #0f766e, #14b8a6);
    }
    .stat-fill-2 {
      background: linear-gradient(90deg, #6d28d9, #8b5cf6);
    }
    .stat-fill-3 {
      background: linear-gradient(90deg, #b45309, #f59e0b);
    }
    .chip-dark {
      background: #1e293b;
      color: #e2e8f0;
      border: 1px solid #334155;
    }
    .election-card {
      background: #0f172a;
      border: 1px solid #1e293b;
      box-shadow: 0 24px 48px -20px #0f172a;
    }
    .voter-icon {
      background: #1e293b;
      border: 1px solid #334155;
    }
    .text-shadow-em {
      text-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
  </style>
</head>
<body class="min-h-screen antialiased text-slate-800">

  <!-- main container with solid grid + depth -->
  <div class="relative min-h-screen overflow-hidden hero-grid-bg">

    <!-- extra solid decorative layer -->
    <div class="absolute inset-0 pointer-events-none opacity-20" style="background-image: linear-gradient(rgba(15,23,42,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(15,23,42,0.03) 1px, transparent 1px); background-size: 48px 48px;"></div>

    <!-- top accent glow -->
    <div class="absolute -top-20 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-emerald-300/20 rounded-full blur-3xl"></div>

    <!-- header -->
    <header class="relative z-20 mx-auto max-w-7xl px-6 py-6 lg:px-10 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-emerald-700 to-teal-700 flex items-center justify-center shadow-xl shadow-emerald-900/30">
          <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="12" cy="12" r="7.5" stroke="currentColor" />
            <circle cx="12" cy="12" r="2.5" fill="currentColor" />
            <path d="M12 2.5V5M12 19v2.5M21.5 12H19M5 12H2.5M18.7 5.3l-1.8 1.8M7.1 16.9l-1.8 1.8M18.7 18.7l-1.8-1.8M7.1 7.1 5.3 5.3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" />
          </svg>
        </div>
        <div class="leading-none">
          <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-emerald-800">Secure · Transparent</p>
          <h1 class="text-2xl font-extrabold tracking-tight text-slate-800">ElectraVote</h1>
        </div>
      </div>
      <nav class="flex items-center gap-3">
        <a href="{{ route('login') }}" class="hidden sm:inline-block text-sm font-semibold text-slate-700 hover:text-emerald-800 transition px-3 py-2 rounded-full hover:bg-white/60">Login</a>
        <a href="{{ route('admin.elections.index') }}" class="inline-flex items-center rounded-full bg-emerald-800 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-900/30 hover:bg-emerald-700 transition">Dashboard</a>
      </nav>
    </header>

    @php
      $candidates = $election?->candidates ?? collect();
      $totalVotes = max((int) ($election?->votes_count ?? 0), 1);
      $topCandidates = $candidates->sortByDesc(fn ($candidate) => (int) $candidate->votes_count)->values();
  @endphp

  <!-- main hero -->
    <main class="relative z-10 max-w-7xl mx-auto px-6 pb-16 lg:px-10 grid lg:grid-cols-[1.2fr_0.9fr] gap-12 items-center">
      
      <!-- left content -->
      <div class="pt-4 lg:pt-8">
        <div class="inline-flex items-center gap-2 rounded-full bg-emerald-100/70 border border-emerald-200/70 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.2em] text-emerald-800 shadow-sm">
          <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span> live · digital democracy
        </div>

        <h2 class="mt-5 text-[4rem] leading-[0.95] font-black tracking-[-0.06em] text-slate-900 drop-shadow-sm lg:text-[6.4rem]">
          Elections, <br class="hidden sm:block" /> <span class="text-emerald-800">built solid.</span>
        </h2>

        <p class="mt-6 max-w-2xl text-xl text-slate-600 leading-relaxed lg:text-2xl">
          Cast, count, and trust. A pristine e-voting core with live results, zero friction, and full auditability.
        </p>

        <div class="mt-10 flex flex-wrap items-center gap-5">
          <a href="{{ route('login') }}" class="inline-flex min-h-[60px] items-center rounded-full bg-emerald-800 px-8 text-lg font-bold text-white shadow-xl shadow-emerald-900/30 hover:bg-emerald-700 transition hover:scale-[1.02]">
            Launch portal
          </a>
          <a href="{{ route('admin.elections.index') }}" class="inline-flex min-h-[56px] items-center rounded-full border border-slate-300 bg-white/70 backdrop-blur-sm px-7 text-lg font-semibold text-slate-700 shadow-sm hover:bg-white/90 transition">
            Admin
          </a>
        </div>

        <!-- mini stats -->
        <div class="mt-10 flex gap-8 text-sm font-medium text-slate-500">
          <div><span class="block text-2xl font-black text-slate-800">{{ number_format((int) ($election?->votes_count ?? 0)) }}</span> total votes</div>
          <div><span class="block text-2xl font-black text-slate-800">{{ $election ? $election->candidates()->count() : 0 }}</span> candidates</div>
          <div><span class="block text-2xl font-black text-slate-800">{{ $election?->isOpen() ? 'Live' : 'Closed' }}</span> status</div>
        </div>
      </div>

      <!-- right: election preview card – SOLID & DENSE -->
      <div class="relative flex justify-center lg:justify-end">
        <div class="w-full max-w-[560px] rounded-3xl border border-slate-200/80 bg-white/80 backdrop-blur-sm p-6 shadow-2xl shadow-slate-200/60 solid-shadow">
          @if ($election)
            <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-5">
              <!-- header -->
              <div class="flex items-start justify-between border-b border-slate-200/70 pb-4">
                <div>
                  <p class="text-[10px] font-extrabold uppercase tracking-[0.26em] text-slate-400">ACTIVE ELECTION</p>
                  <h3 class="mt-3 text-2xl font-extrabold tracking-tight text-slate-900">{{ $election->title }}</h3>
                  <p class="text-sm text-slate-500">{{ $election->description ?: 'Live results from the latest election.' }}</p>
                </div>
                <span class="badge-open inline-flex items-center rounded-full px-3 py-1 text-[11px] uppercase tracking-wide">● {{ $election->isOpen() ? 'open' : 'closed' }}</span>
              </div>

              <!-- candidates – solid cards -->
              <div class="mt-5 space-y-4">
                @forelse ($topCandidates as $candidate)
                  @php
                      $percentage = $totalVotes > 0 ? round(($candidate->votes_count / $totalVotes) * 100) : 0;
                      $barClass = match ($loop->iteration % 3) {
                          0 => 'stat-fill-3',
                          1 => 'stat-fill',
                          default => 'stat-fill-2',
                      };
                  @endphp

                  <div class="candidate-card rounded-xl p-4">
                    <div class="flex items-center justify-between gap-3">
                      <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">candidate</p>
                        <p class="mt-1 text-lg font-bold text-slate-800">{{ $candidate->name }}</p>
                      </div>
                      <span class="min-w-[52px] rounded-lg bg-slate-100 px-2 py-1 text-center text-sm font-bold text-slate-700">{{ $percentage }}%</span>
                    </div>
                    <div class="stat-bar mt-3">
                      <div class="{{ $barClass }} w-[{{ $percentage }}%]"></div>
                    </div>
                  </div>
                @empty
                  <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500">
                    No candidates have been added for this election yet.
                  </div>
                @endforelse
              </div>

              <!-- footer: timestamp + turnout -->
              <div class="mt-5 flex items-center justify-between border-t border-slate-200/60 pt-4 text-xs text-slate-500">
                <span class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-600"></span> {{ number_format((int) $election->votes_count) }} votes cast</span>
                <span class="font-mono text-slate-400">{{ $election->ends_at ? '⏱ ends ' . $election->ends_at->diffForHumans() : '⏱ no close date' }}</span>
              </div>
            </div>
          @else
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white/70 p-8 text-center text-slate-600">
              <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">No active election</p>
              <h3 class="mt-3 text-2xl font-bold text-slate-800">Live results will appear here</h3>
              <p class="mt-2 text-sm text-slate-500">Create an election and add candidates from the admin dashboard.</p>
            </div>
          @endif
        </div>

        <!-- floating badge – solid trust -->
        <div class="absolute -bottom-4 -left-4 hidden lg:flex rounded-full bg-slate-900/5 border border-slate-200/80 backdrop-blur-sm px-4 py-2 text-xs font-bold text-slate-700 shadow-lg">
          <span class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> blockchain verified</span>
        </div>
      </div>
    </main>

    <!-- footer-ish trust line -->
    <div class="relative z-10 max-w-7xl mx-auto px-6 pb-8 flex flex-wrap items-center justify-between gap-4 text-xs text-slate-500 border-t border-slate-200/60 pt-6 mt-6">
      <div class="flex items-center gap-6">
        <span class="font-bold text-emerald-800">🔒 end-to-end encrypted</span>
        <span>•</span>
        <span>audited by <span class="font-semibold text-slate-700">SecureBallot</span></span>
        <span>•</span>
        <span>ISO 27001</span>
      </div>
      <div class="flex gap-4">
        <a href="#" class="hover:text-emerald-800 transition">Privacy</a>
        <a href="#" class="hover:text-emerald-800 transition">Terms</a>
        <a href="#" class="hover:text-emerald-800 transition">Support</a>
      </div>
    </div>

  </div>
</body>
</html>
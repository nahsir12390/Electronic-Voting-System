<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Electronic Voting System') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen antialiased" style="background: linear-gradient(180deg, #eafaf5 0%, #edf7f4 100%);">
        <div class="relative min-h-screen overflow-hidden" style="background-image: linear-gradient(rgba(15,23,42,0.02) 1px, transparent 1px), linear-gradient(90deg, rgba(15,23,42,0.02) 1px, transparent 1px); background-size: 32px 32px;">
            <div class="absolute inset-x-0 top-0 h-[220px] bg-gradient-to-b from-emerald-300/35 to-transparent"></div>

            <header class="relative z-10 mx-auto flex max-w-[1500px] items-center justify-between px-8 py-7 lg:px-12">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-[0_12px_24px_rgba(16,185,129,0.35)]">
                        <svg viewBox="0 0 24 24" class="h-6 w-6 text-white" fill="none" aria-hidden="true">
                            <circle cx="12" cy="12" r="7.5" stroke="currentColor" stroke-width="1.8"/>
                            <circle cx="12" cy="12" r="2.5" fill="currentColor"/>
                            <path d="M12 2.5V5M12 19v2.5M21.5 12H19M5 12H2.5M18.7 5.3l-1.8 1.8M7.1 16.9l-1.8 1.8M18.7 18.7l-1.8-1.8M7.1 7.1 5.3 5.3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="leading-none">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-700">SECURE VOTING</p>
                        <h1 class="mt-2 text-[1.9rem] font-bold tracking-tight text-slate-800">ElectraVote</h1>
                    </div>
                </div>

                @if (Route::has('login'))
                    <nav class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-full bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow-sm transition hover:bg-emerald-400">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center rounded-full px-3 py-2 text-base font-medium text-slate-700 transition hover:text-slate-900">
                                Login
                            </a>
                        @endauth
                    </nav>
                @endif
            </header>

            <main class="relative z-10 mx-auto grid max-w-[1500px] gap-10 px-8 pb-20 pt-8 lg:grid-cols-[1.25fr_0.75fr] lg:px-12 lg:pt-12">
                <div class="flex flex-col justify-center pt-2">
                    <div class="mb-6 inline-flex w-fit rounded-full border border-emerald-200 bg-emerald-100/80 px-5 py-2 text-[12px] font-semibold uppercase tracking-[0.22em] text-emerald-700 shadow-sm">
                        DIGITAL DEMOCRACY PLATFORM
                    </div>

                    <h2 class="max-w-[760px] text-[3.5rem] font-black leading-[0.9] tracking-[-0.06em] text-white drop-shadow-[0_10px_20px_rgba(26,44,39,0.15)] sm:text-[5rem] lg:text-[7.2rem]">
                        Modern elections,<br>
                        built on trust.
                    </h2>

                    <p class="mt-7 max-w-[760px] text-xl leading-8 text-slate-600 lg:text-[1.7rem] lg:leading-[2.3rem]">
                        Manage ballots, monitor active elections, and ensure transparent voting with a clean, secure, and highly organized electronic voting experience.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-5">
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="inline-flex min-h-[60px] items-center rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 px-7 text-xl font-bold text-slate-950 shadow-[0_16px_32px_rgba(16,185,129,0.32)] transition hover:scale-[1.01] hover:shadow-[0_18px_34px_rgba(16,185,129,0.36)]">
                                Access portal
                            </a>
                        @endif
                        <a href="{{ route('admin.elections.index') }}" class="inline-flex min-h-[60px] items-center rounded-full border border-slate-300 bg-white/40 px-7 text-xl font-semibold text-slate-700 shadow-sm backdrop-blur-sm transition hover:bg-white/60">
                            Admin overview
                        </a>
                    </div>
                </div>

                <div class="relative flex items-center justify-center pt-3 lg:pt-0">
                    <div class="w-full max-w-[610px] rounded-[28px] border border-slate-300 bg-slate-900 p-5 shadow-[0_28px_48px_rgba(15,23,42,0.28)]">
                        <div class="rounded-[24px] border border-slate-700 bg-slate-950 p-5">
                            <div class="flex items-center justify-between border-b border-slate-700 pb-4">
                                <div>
                                    <p class="text-[12px] font-bold uppercase tracking-[0.22em] text-slate-400">ACTIVE ELECTION</p>
                                    <h3 class="mt-4 text-[2.2rem] font-bold leading-tight tracking-[-0.05em] text-white">Student Council Vote</h3>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-emerald-500/15 px-3 py-1.5 text-[12px] font-bold text-emerald-300">Open</span>
                            </div>

                            <div class="mt-5 space-y-4">
                                <div class="rounded-2xl border border-slate-700 bg-slate-900/80 p-4">
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="min-w-0">
                                            <p class="text-[14px] font-medium text-slate-400">Candidate</p>
                                            <p class="mt-2 text-[1.05rem] font-semibold text-white">Daniel Reed</p>
                                        </div>
                                        <span class="min-w-[52px] rounded-xl bg-slate-700 px-2 py-1 text-center text-[12px] font-bold text-slate-200">42%</span>
                                    </div>
                                    <div class="mt-4 h-3 overflow-hidden rounded-full bg-slate-800">
                                        <div class="h-full w-[42%] rounded-full bg-gradient-to-r from-emerald-400 to-teal-500"></div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-700 bg-slate-900/80 p-4">
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="min-w-0">
                                            <p class="text-[14px] font-medium text-slate-400">Candidate</p>
                                            <p class="mt-2 text-[1.05rem] font-semibold text-white">Aisha Bello</p>
                                        </div>
                                        <span class="min-w-[52px] rounded-xl bg-slate-700 px-2 py-1 text-center text-[12px] font-bold text-slate-200">35%</span>
                                    </div>
                                    <div class="mt-4 h-3 overflow-hidden rounded-full bg-slate-800">
                                        <div class="h-full w-[35%] rounded-full bg-gradient-to-r from-violet-500 to-indigo-500"></div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-700 bg-slate-900/80 p-4">
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="min-w-0">
                                            <p class="text-[14px] font-medium text-slate-400">Candidate</p>
                                            <p class="mt-2 text-[1.05rem] font-semibold text-white">Marcus Lee</p>
                                        </div>
                                        <span class="min-w-[52px] rounded-xl bg-slate-700 px-2 py-1 text-center text-[12px] font-bold text-slate-200">23%</span>
                                    </div>
                                    <div class="mt-4 h-3 overflow-hidden rounded-full bg-slate-800">
                                        <div class="h-full w-[23%] rounded-full bg-gradient-to-r from-amber-400 to-orange-500"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>

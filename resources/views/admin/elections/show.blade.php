<x-layouts::app :title="$election->title.' Results'">
    <div class="ev-page">
        <header class="ev-hero">
            <div class="grid gap-6 p-6 lg:grid-cols-[1fr_360px] lg:items-end">
                <div>
                    <a href="{{ route('admin.elections.index') }}" class="text-sm font-medium text-emerald-100 hover:text-white">{{ __('Back to command center') }}</a>
                    <p class="ev-kicker mt-5">{{ __('Certified tally view') }}</p>
                    <h1 class="mt-3 text-3xl font-semibold text-white md:text-4xl">{{ $election->title }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-emerald-50/85">
                        {{ $election->description ?? __('Election result summary and candidate vote distribution.') }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="ev-stat">
                        <p class="text-xs text-emerald-50/75">{{ __('Total votes') }}</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $election->votes_count }}</p>
                    </div>
                    <div class="ev-stat">
                        <p class="text-xs text-emerald-50/75">{{ __('Status') }}</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $election->isOpen() ? __('Open') : __('Closed') }}</p>
                    </div>
                </div>
            </div>
        </header>

        <section class="ev-panel p-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-slate-950 dark:text-white">{{ __('Candidate results') }}</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Vote counts and percentage share by candidate.') }}</p>
                </div>
                <span class="ev-chip border-slate-200 bg-slate-50 text-slate-700 dark:border-zinc-700 dark:bg-zinc-950 dark:text-slate-200">
                    {{ $election->candidates->count() }} {{ __('candidates') }}
                </span>
            </div>

            <div class="mt-5 grid gap-3">
                @forelse ($election->candidates as $candidate)
                    @php
                        $percentage = $election->votes_count > 0 ? round(($candidate->votes_count / $election->votes_count) * 100, 1) : 0;
                    @endphp

                    <article class="ev-subpanel p-4">
                        <div class="grid gap-4 md:grid-cols-[1fr_120px] md:items-center">
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <div class="ev-ballot-mark">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                                    <div>
                                        <h3 class="font-semibold text-slate-950 dark:text-white">{{ $candidate->name }}</h3>
                                        @if ($candidate->position)
                                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $candidate->position }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-4 ev-progress">
                                    <div class="ev-progress-bar" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>

                            <div class="text-left md:text-right">
                                <p class="text-2xl font-semibold text-slate-950 dark:text-white">{{ $candidate->votes_count }}</p>
                                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $percentage }}%</p>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('No candidates have been added yet.') }}</p>
                @endforelse
            </div>
        </section>

        <section class="ev-panel p-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-slate-950 dark:text-white">{{ __('Voter ledger') }}</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Review who voted, the selected candidate, and the recorded receipt hash.') }}</p>
                </div>
                <span class="ev-chip border-slate-200 bg-slate-50 text-slate-700 dark:border-zinc-700 dark:bg-zinc-950 dark:text-slate-200">
                    {{ $voterLedger->count() }} {{ __('voters recorded') }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.elections.show', $election) }}" class="mt-5 grid gap-3 border-b border-slate-200 pb-5 dark:border-zinc-700 lg:grid-cols-[minmax(0,1.3fr)_minmax(220px,0.8fr)_auto]">
                <label class="grid gap-2 text-sm">
                    <span class="font-medium text-slate-700 dark:text-slate-200">{{ __('Search voter') }}</span>
                    <input
                        name="search"
                        value="{{ $ledgerFilters['search'] }}"
                        class="ev-input"
                        placeholder="{{ __('Filter by voter name or email') }}"
                    >
                </label>

                <label class="grid gap-2 text-sm">
                    <span class="font-medium text-slate-700 dark:text-slate-200">{{ __('Candidate') }}</span>
                    <select name="candidate" class="ev-input">
                        <option value="">{{ __('All candidates') }}</option>
                        @foreach ($election->candidates as $candidate)
                            <option value="{{ $candidate->id }}" @selected($ledgerFilters['candidate'] === $candidate->id)>
                                {{ $candidate->name }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <div class="flex flex-col gap-3 self-end sm:flex-row lg:justify-end">
                    <button class="ev-btn ev-btn-primary w-full sm:w-auto">{{ __('Apply filters') }}</button>
                    @if ($ledgerFilters['search'] !== '' || $ledgerFilters['candidate'])
                        <a href="{{ route('admin.elections.show', $election) }}" class="ev-btn ev-btn-secondary w-full sm:w-auto">{{ __('Clear') }}</a>
                    @endif
                </div>
            </form>

            <div class="mt-5 grid gap-3">
                @forelse ($voterLedger as $vote)
                    <article class="ev-subpanel p-4">
                        <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-start">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('Voter') }}</p>
                                <p class="mt-2 font-semibold text-slate-950 dark:text-white">{{ $vote->user?->name ?? __('Deleted user') }}</p>
                                <p class="mt-1 break-all text-sm text-slate-500 dark:text-slate-400">{{ $vote->user?->email ?? __('No email available') }}</p>
                            </div>

                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('Ballot selection') }}</p>
                                <p class="mt-2 font-semibold text-slate-950 dark:text-white">{{ $vote->candidate?->name ?? __('Candidate removed') }}</p>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $vote->created_at?->format('M j, Y g:i A') ?? __('Timestamp unavailable') }}</p>
                            </div>

                            <div class="min-w-0 lg:text-right">
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('Receipt hash') }}</p>
                                <p class="mt-2 break-all font-mono text-xs leading-5 text-slate-600 dark:text-slate-300">{{ $vote->vote_hash }}</p>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        {{ $ledgerFilters['search'] !== '' || $ledgerFilters['candidate'] ? __('No votes match the current filters.') : __('No votes have been recorded for this election yet.') }}
                    </p>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts::app>

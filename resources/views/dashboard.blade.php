<x-layouts::app :title="__('Ballot Center')">
    <div class="ev-page">
        <header class="ev-hero">
            <div class="grid gap-6 p-6 lg:grid-cols-[1fr_420px] lg:items-end">
                <div>
                    <p class="ev-kicker">{{ __('Verified voter workspace') }}</p>
                    <h1 class="mt-3 max-w-3xl text-3xl font-semibold text-white md:text-4xl">
                        {{ __('Cast your ballot with confidence.') }}
                    </h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-emerald-50/85">
                        {{ __('Each authenticated voter can submit one ballot per election. Confirmed votes receive a receipt hash for audit verification while protecting voter access records.') }}
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="ev-stat">
                        <p class="text-xs text-emerald-50/75">{{ __('Elections') }}</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $elections->count() }}</p>
                    </div>
                    <div class="ev-stat">
                        <p class="text-xs text-emerald-50/75">{{ __('Open') }}</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $elections->filter->isOpen()->count() }}</p>
                    </div>
                    <div class="ev-stat">
                        <p class="text-xs text-emerald-50/75">{{ __('Submitted') }}</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $votesByElection->count() }}</p>
                    </div>
                </div>
            </div>
        </header>

        @if (session('status'))
            <div class="ev-receipt">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-900 dark:border-red-900 dark:bg-red-950 dark:text-red-100">
                {{ $errors->first() }}
            </div>
        @endif

        <main class="grid gap-5">
            @forelse ($elections as $election)
                @php
                    $userVote = $votesByElection->get($election->id);
                    $totalVotes = max($election->votes_count, 1);
                @endphp

                <section class="ev-panel">
                    <div class="grid gap-5 border-b border-slate-200 p-5 dark:border-zinc-700 lg:grid-cols-[1fr_320px]">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-xl font-semibold text-slate-950 dark:text-white">{{ $election->title }}</h2>
                                <span class="ev-chip {{ $election->isOpen() ? 'ev-chip-open' : 'ev-chip-closed' }}">
                                    {{ $election->isOpen() ? __('Open') : __('Closed') }}
                                </span>
                            </div>

                            @if ($election->description)
                                <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-300">{{ $election->description }}</p>
                            @endif

                            <div class="mt-4 grid gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 sm:grid-cols-2">
                                <p>{{ __('Start:') }} {{ $election->starts_at?->format('M j, Y g:i A') ?? __('Any time') }}</p>
                                <p>{{ __('End:') }} {{ $election->ends_at?->format('M j, Y g:i A') ?? __('No end date') }}</p>
                            </div>
                        </div>

                        <div class="{{ $userVote ? 'ev-receipt' : 'ev-subpanel p-4' }}">
                            @if ($userVote)
                                <p class="text-xs font-semibold uppercase tracking-wider">{{ __('Ballot recorded') }}</p>
                                <p class="mt-2 font-semibold">{{ $userVote->candidate->name }}</p>
                                <p class="mt-2 break-all font-mono text-[11px] leading-5 opacity-80">{{ $userVote->vote_hash }}</p>
                            @else
                                <p class="text-sm font-semibold text-slate-950 dark:text-white">{{ __('Eligibility status') }}</p>
                                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                                    {{ $election->isOpen() ? __('You are eligible to vote in this open election.') : __('Voting is currently unavailable for this election.') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="grid gap-3 p-5 lg:grid-cols-2">
                        @forelse ($election->candidates as $candidate)
                            <article class="ev-subpanel p-4">
                                <div class="flex gap-4">
                                    <div class="ev-ballot-mark">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-start justify-between gap-3">
                                            <div>
                                                <h3 class="text-base font-semibold text-slate-950 dark:text-white">{{ $candidate->name }}</h3>
                                                @if ($candidate->position)
                                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $candidate->position }}</p>
                                                @endif
                                            </div>
                                            @if ($election->show_results)
                                                <span class="ev-chip border-slate-200 bg-white text-slate-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-slate-200">
                                                    {{ $candidate->votes_count }} {{ __('votes') }}
                                                </span>
                                            @endif
                                        </div>

                                        @if ($candidate->manifesto)
                                            <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ $candidate->manifesto }}</p>
                                        @endif

                                        @if ($election->show_results)
                                            <div class="mt-4">
                                                <div class="ev-progress">
                                                    <div class="ev-progress-bar" style="width: {{ round(($candidate->votes_count / $totalVotes) * 100) }}%"></div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (! $userVote && $election->isOpen())
                                            <form method="POST" action="{{ route('votes.store', $election) }}" class="mt-4">
                                                @csrf
                                                <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">
                                                <button type="submit" class="ev-btn ev-btn-dark w-full">
                                                    {{ __('Select and submit ballot') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @empty
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('No candidates have been added yet.') }}</p>
                        @endforelse
                    </div>
                </section>
            @empty
                <div class="ev-panel p-10 text-center">
                    <h2 class="text-xl font-semibold text-slate-950 dark:text-white">{{ __('No active ballots yet') }}</h2>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ __('An administrator can create the first election from the Command Center.') }}</p>
                </div>
            @endforelse
        </main>
    </div>
</x-layouts::app>

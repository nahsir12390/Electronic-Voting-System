<x-layouts::app :title="__('Voter Access')">
    <div class="ev-page">
        <header class="ev-hero">
            <div class="grid gap-6 p-6 lg:grid-cols-[1fr_360px] lg:items-end">
                <div>
                    <p class="ev-kicker">{{ __('Approved voter access') }}</p>
                    <h1 class="mt-3 text-3xl font-semibold text-white md:text-4xl">{{ __('Voter account management') }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-emerald-50/85">
                        {{ __('Create approved voter accounts, review turnout readiness, and remove unused accounts before the ballot opens.') }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="ev-stat">
                        <p class="text-xs text-emerald-50/75">{{ __('Approved voters') }}</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $voters->count() }}</p>
                    </div>
                    <div class="ev-stat">
                        <p class="text-xs text-emerald-50/75">{{ __('Already voted') }}</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $votedCount }}</p>
                    </div>
                </div>
            </div>
        </header>

        @if (session('status'))
            <div class="ev-receipt">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-900 dark:border-red-900 dark:bg-red-950 dark:text-red-100">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid gap-5 xl:grid-cols-[380px_1fr]">
            <section class="ev-panel p-5">
                <div>
                    <h2 class="text-lg font-semibold text-slate-950 dark:text-white">{{ __('Create voter account') }}</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('New voter accounts are verified immediately so they can log in and vote without a separate registration step.') }}</p>
                </div>

                <form method="POST" action="{{ route('admin.voters.store') }}" class="mt-5 grid gap-4">
                    @csrf
                    <label class="grid gap-2 text-sm">
                        <span class="font-medium text-slate-700 dark:text-slate-200">{{ __('Full name') }}</span>
                        <input name="name" required class="ev-input" value="{{ old('name') }}" placeholder="{{ __('Enter voter name') }}">
                    </label>

                    <label class="grid gap-2 text-sm">
                        <span class="font-medium text-slate-700 dark:text-slate-200">{{ __('Email address') }}</span>
                        <input name="email" type="email" required class="ev-input" value="{{ old('email') }}" placeholder="{{ __('voter@example.com') }}">
                    </label>

                    <label class="grid gap-2 text-sm">
                        <span class="font-medium text-slate-700 dark:text-slate-200">{{ __('Temporary password') }}</span>
                        <input name="password" type="password" required class="ev-input" placeholder="{{ __('Minimum 8 characters') }}">
                    </label>

                    <label class="grid gap-2 text-sm">
                        <span class="font-medium text-slate-700 dark:text-slate-200">{{ __('Confirm password') }}</span>
                        <input name="password_confirmation" type="password" required class="ev-input" placeholder="{{ __('Repeat the password') }}">
                    </label>

                    <button class="ev-btn ev-btn-primary w-full">{{ __('Create voter') }}</button>
                </form>
            </section>

            <section class="ev-panel p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-950 dark:text-white">{{ __('Approved voters') }}</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Only these non-admin accounts can log in as voters.') }}</p>
                    </div>
                    <span class="ev-chip border-slate-200 bg-slate-50 text-slate-700 dark:border-zinc-700 dark:bg-zinc-950 dark:text-slate-200">
                        {{ $voters->count() }} {{ __('accounts') }}
                    </span>
                </div>

                <div class="mt-5 grid gap-3">
                    @forelse ($voters as $voter)
                        <article class="ev-subpanel p-4">
                            <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-start">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-semibold text-slate-950 dark:text-white">{{ $voter->name }}</h3>
                                        <span class="ev-chip {{ $voter->votes_count > 0 ? 'ev-chip-open' : 'ev-chip-closed' }}">
                                            {{ $voter->votes_count > 0 ? __('Voted') : __('Ready') }}
                                        </span>
                                    </div>
                                    <p class="mt-2 break-all text-sm text-slate-500 dark:text-slate-400">{{ $voter->email }}</p>
                                    <div class="mt-3 flex flex-wrap gap-4 text-sm text-slate-500 dark:text-slate-400">
                                        <span>{{ __('Created:') }} {{ $voter->created_at?->format('M j, Y g:i A') }}</span>
                                        <span>{{ __('Votes cast:') }} {{ $voter->votes_count }}</span>
                                        <span>{{ __('Access:') }} {{ $voter->email_verified_at ? __('Verified') : __('Pending') }}</span>
                                    </div>
                                </div>

                                <div class="lg:text-right">
                                    <form method="POST" action="{{ route('admin.voters.destroy', $voter) }}" onsubmit="return confirm('{{ __('Delete this voter account?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="ev-btn ev-btn-danger w-full lg:w-auto">{{ __('Delete voter') }}</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="ev-subpanel p-6 text-center">
                            <h3 class="text-base font-semibold text-slate-950 dark:text-white">{{ __('No voter accounts yet') }}</h3>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ __('Create the first voter account from the form on this page.') }}</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-layouts::app>
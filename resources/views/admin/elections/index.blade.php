<x-layouts::app :title="__('Command Center')">
    <div class="ev-page">
        <header class="ev-hero">
            <div class="flex flex-col gap-5 p-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="ev-kicker">{{ __('Administrator command center') }}</p>
                    <h1 class="mt-3 text-3xl font-semibold text-white md:text-4xl">{{ __('Election operations') }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-emerald-50/85">
                        {{ __('Create elections, manage candidate lists, control ballot availability, and review results from one secure workspace.') }}
                    </p>
                </div>
                <div class="ev-stat w-full lg:w-72">
                    <p class="text-xs text-emerald-50/75">{{ __('System status') }}</p>
                    <div class="mt-3 flex items-center justify-between gap-3">
                        <span class="font-semibold">{{ __('Online') }}</span>
                        <span class="size-2.5 rounded-full bg-emerald-300"></span>
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

        <section class="ev-panel p-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-slate-950 dark:text-white">{{ __('Create election') }}</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Define the ballot window and result visibility.') }}</p>
                </div>
                <span class="ev-chip border-cyan-200 bg-cyan-50 text-cyan-700 dark:border-cyan-900 dark:bg-cyan-950 dark:text-cyan-300">{{ __('New ballot') }}</span>
            </div>

            <form method="POST" action="{{ route('admin.elections.store') }}" class="mt-5 grid gap-4 md:grid-cols-2">
                @csrf
                <label class="grid gap-2 text-sm">
                    <span class="font-medium text-slate-700 dark:text-slate-200">{{ __('Title') }}</span>
                    <input name="title" required class="ev-input" value="{{ old('title') }}">
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium text-slate-700 dark:text-slate-200">{{ __('Description') }}</span>
                    <input name="description" class="ev-input" value="{{ old('description') }}">
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium text-slate-700 dark:text-slate-200">{{ __('Starts At') }}</span>
                    <input name="starts_at" type="datetime-local" class="ev-input" value="{{ old('starts_at') }}">
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium text-slate-700 dark:text-slate-200">{{ __('Ends At') }}</span>
                    <input name="ends_at" type="datetime-local" class="ev-input" value="{{ old('ends_at') }}">
                </label>
                <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-200">
                    <input name="is_active" type="checkbox" value="1" checked class="size-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span>{{ __('Allow voting when schedule permits') }}</span>
                </label>
                <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-200">
                    <input name="show_results" type="checkbox" value="1" checked class="size-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span>{{ __('Show live results to voters') }}</span>
                </label>
                <div class="md:col-span-2">
                    <button class="ev-btn ev-btn-primary">{{ __('Create election') }}</button>
                </div>
            </form>
        </section>

        <div class="grid gap-5">
            @forelse ($elections as $election)
                <section class="ev-panel">
                    <div class="grid gap-4 border-b border-slate-200 p-5 dark:border-zinc-700 lg:grid-cols-[1fr_auto]">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-xl font-semibold text-slate-950 dark:text-white">{{ $election->title }}</h2>
                                <span class="ev-chip {{ $election->isOpen() ? 'ev-chip-open' : 'ev-chip-closed' }}">{{ $election->isOpen() ? __('Open') : __('Closed') }}</span>
                            </div>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                                {{ $election->candidates_count }} {{ __('candidates') }} &middot; {{ $election->votes_count }} {{ __('votes') }}
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('admin.elections.show', $election) }}" class="ev-btn ev-btn-secondary">{{ __('View results') }}</a>
                            <form method="POST" action="{{ route('admin.elections.destroy', $election) }}" onsubmit="return confirm('{{ __('Delete this election and all recorded votes?') }}')">
                                @csrf
                                @method('DELETE')
                                <button class="ev-btn ev-btn-danger">{{ __('Delete') }}</button>
                            </form>
                        </div>
                    </div>

                    <div class="grid gap-6 p-5 xl:grid-cols-[1fr_1.1fr]">
                        <form method="POST" action="{{ route('admin.elections.update', $election) }}" class="ev-subpanel grid gap-4 p-4">
                            @csrf
                            @method('PATCH')
                            <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('Election settings') }}</h3>
                            <label class="grid gap-2 text-sm">
                                <span class="font-medium text-slate-700 dark:text-slate-200">{{ __('Title') }}</span>
                                <input name="title" required class="ev-input" value="{{ old('title', $election->title) }}">
                            </label>
                            <label class="grid gap-2 text-sm">
                                <span class="font-medium text-slate-700 dark:text-slate-200">{{ __('Description') }}</span>
                                <input name="description" class="ev-input" value="{{ old('description', $election->description) }}">
                            </label>
                            <div class="grid gap-4 md:grid-cols-2">
                                <label class="grid gap-2 text-sm">
                                    <span class="font-medium text-slate-700 dark:text-slate-200">{{ __('Starts At') }}</span>
                                    <input name="starts_at" type="datetime-local" class="ev-input" value="{{ old('starts_at', $election->starts_at?->format('Y-m-d\TH:i')) }}">
                                </label>
                                <label class="grid gap-2 text-sm">
                                    <span class="font-medium text-slate-700 dark:text-slate-200">{{ __('Ends At') }}</span>
                                    <input name="ends_at" type="datetime-local" class="ev-input" value="{{ old('ends_at', $election->ends_at?->format('Y-m-d\TH:i')) }}">
                                </label>
                            </div>
                            <div class="grid gap-3 text-sm text-slate-700 dark:text-slate-200 sm:grid-cols-2">
                                <label class="flex items-center gap-2">
                                    <input name="is_active" type="checkbox" value="1" @checked($election->is_active) class="size-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    <span>{{ __('Active') }}</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input name="show_results" type="checkbox" value="1" @checked($election->show_results) class="size-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    <span>{{ __('Show results') }}</span>
                                </label>
                            </div>
                            <button class="ev-btn ev-btn-dark w-fit">{{ __('Save election') }}</button>
                        </form>

                        <div class="grid gap-4">
                            <div class="flex items-center justify-between gap-3">
                                <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('Candidate roster') }}</h3>
                                <span class="text-sm text-slate-500 dark:text-slate-400">{{ $election->candidates_count }} {{ __('total') }}</span>
                            </div>

                            @foreach ($election->candidates as $candidate)
                                <div class="ev-subpanel p-4">
                                    <form method="POST" action="{{ route('admin.candidates.update', $candidate) }}" class="grid gap-3 lg:grid-cols-2">
                                        @csrf
                                        @method('PATCH')
                                        <input name="name" required class="ev-input" value="{{ old('name', $candidate->name) }}">
                                        <input name="position" class="ev-input" value="{{ old('position', $candidate->position) }}" placeholder="{{ __('Position') }}">
                                        <input name="manifesto" class="ev-input lg:col-span-2" value="{{ old('manifesto', $candidate->manifesto) }}" placeholder="{{ __('Manifesto') }}">
                                        <div class="lg:col-span-2 lg:flex lg:justify-end">
                                            <button class="ev-btn ev-btn-primary w-full lg:w-auto">{{ __('Update') }}</button>
                                        </div>
                                    </form>

                                    <div class="mt-3 flex flex-wrap items-center justify-between gap-3 text-sm text-slate-500 dark:text-slate-400">
                                        <span>{{ $candidate->votes_count }} {{ __('votes recorded') }}</span>
                                        <form method="POST" action="{{ route('admin.candidates.destroy', $candidate) }}" onsubmit="return confirm('{{ __('Delete this candidate?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="font-medium text-red-700 hover:underline dark:text-red-300">{{ __('Delete candidate') }}</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach

                            <form method="POST" action="{{ route('admin.candidates.store', $election) }}" class="grid gap-3 border-t border-slate-200 pt-4 dark:border-zinc-700 lg:grid-cols-2">
                                @csrf
                                <input name="name" required placeholder="{{ __('Candidate name') }}" class="ev-input">
                                <input name="position" placeholder="{{ __('Position') }}" class="ev-input">
                                <input name="manifesto" placeholder="{{ __('Manifesto') }}" class="ev-input lg:col-span-2">
                                <div class="lg:col-span-2 lg:flex lg:justify-end">
                                    <button class="ev-btn ev-btn-primary w-full lg:w-auto">{{ __('Add') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            @empty
                <div class="ev-panel p-10 text-center">
                    <h2 class="text-xl font-semibold text-slate-950 dark:text-white">{{ __('No elections yet') }}</h2>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ __('Create the first election above.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts::app>

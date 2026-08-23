@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="ElectionGuard" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-lg bg-emerald-700 text-white shadow-sm dark:bg-emerald-500 dark:text-zinc-950">
            <x-app-logo-icon class="size-5" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="ElectionGuard" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-lg bg-emerald-700 text-white shadow-sm dark:bg-emerald-500 dark:text-zinc-950">
            <x-app-logo-icon class="size-5" />
        </x-slot>
    </flux:brand>
@endif

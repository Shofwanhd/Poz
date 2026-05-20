@props([
    'sidebar' => false,
])

@if ($sidebar)
    <flux:sidebar.brand name="{{ $appSetting?->NamaToko ?? 'Poz' }}" {{ $attributes }}>
        <x-slot name="logo"
            class="flex aspect-square bg-white size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
            <x-app-logo-icons class="size-10 fill-current text-white dark:text-black" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="{{ $appSetting?->NamaToko ?? 'Poz' }}" {{ $attributes }}>
        <x-slot name="logo"
            class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
            <x-app-logo-icons class="size-10 fill-current text-white dark:text-black" />
        </x-slot>
    </flux:brand>
@endif

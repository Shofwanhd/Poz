@props(['title', 'description', 'link'])


<a href="{{ $link }}" aria-label="{{ $title }}">
    <flux:card size="sm" class="hover:bg-zinc-50 dark:hover:bg-zinc-600">
        <flux:heading class="flex items-center gap-2">{{ $title }}</flux:heading>
        <flux:text class="mt-2">{{ $description }}</flux:text>
    </flux:card>
</a>
@props(['title', 'title2', 'description'])

<div class="flex w-full flex-col text-center">
    <flux:heading size="xl">{{ $title }}</flux:heading>
    <flux:heading size="xl">{{ $title2 }}</flux:heading>
    <flux:subheading>{{ $description }}</flux:subheading>
</div>

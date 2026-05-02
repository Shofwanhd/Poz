@props(['title', 'link'])

<div class="flex items-center gap-4">
    <a href="{{ $link }}">
        <button onclick="history.back()">
            <flux:icon.arrow-left />
        </button>
    </a>

    <h1 class="text-lg font-semibold">
        {{ $title }}
    </h1>
</div>
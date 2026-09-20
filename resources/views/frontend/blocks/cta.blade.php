<div class="mx-auto max-w-4xl px-6 py-8">
    <div class="flex flex-col items-start gap-4 rounded-lg bg-primary p-8 text-white sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-xl font-bold">{{ $data['heading'] ?? '' }}</h2>
        @if ($data['button_url'] ?? null)
            <a href="{{ $data['button_url'] }}" wire:navigate
               class="shrink-0 rounded-full bg-secondary px-6 py-2.5 font-semibold text-white transition hover:opacity-90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                {{ $data['button_label'] ?? '' }}
            </a>
        @endif
    </div>
</div>

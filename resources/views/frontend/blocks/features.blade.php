<section class="mx-auto max-w-6xl px-6 py-16">
    @if ($data['heading'] ?? null)
        <h2 class="text-center text-3xl font-bold tracking-tight text-slate-900">{{ $data['heading'] }}</h2>
    @endif
    <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($data['items'] ?? [] as $item)
            <div>
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-primary/10 text-primary shadow-sm shadow-primary/10">
                    @svg($item['icon'] ?? 'heroicon-o-check-badge', 'h-5 w-5')
                </span>
                <h3 class="mt-4 font-semibold text-slate-900">{{ $item['title'] ?? '' }}</h3>
                @if ($item['description'] ?? null)
                    <p class="mt-2 text-sm text-slate-600">{{ $item['description'] }}</p>
                @endif
            </div>
        @endforeach
    </div>
</section>

@php
    $trips = \App\Models\Trip::query()
        ->with(['route', 'category', 'publishedReviews'])
        ->when(
            $data['category_slug'] ?? null,
            fn ($query, $slug) => $query->whereHas('category', fn ($q) => $q->where('slug', $slug)),
        )
        ->latest('published_at')
        ->get()
        ->filter->isPublished()
        ->take((int) ($data['count'] ?? 6));
    $gridClass = match ((int) ($data['columns'] ?? 3)) {
        2 => 'sm:grid-cols-2 lg:grid-cols-2',
        4 => 'sm:grid-cols-2 lg:grid-cols-4',
        default => 'sm:grid-cols-2 lg:grid-cols-3',
    };
@endphp
@if ($trips->isNotEmpty())
    <section class="mx-auto max-w-6xl px-6 py-16">
        @if ($data['heading'] ?? null)
            <h2 class="text-center text-3xl font-bold tracking-tight text-slate-900">{{ $data['heading'] }}</h2>
        @endif
        <div class="mt-10 grid gap-6 {{ $gridClass }}">
            @foreach ($trips as $trip)
                <x-trip-card :trip="$trip" />
            @endforeach
        </div>
    </section>
@endif

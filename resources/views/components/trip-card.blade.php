@props(['trip'])

@php
    $displayRating = $trip->displayRating();
    $facts = array_filter([
        ['icon' => 'clock', 'value' => $trip->duration],
        ['icon' => 'chart-bar', 'value' => $trip->difficulty?->getLabel()],
        ['icon' => 'arrow-trending-up', 'value' => $trip->altitude],
    ], fn (array $fact): bool => filled($fact['value']));
@endphp

@if ($trip->route)
    <a href="{{ url($trip->route->full_path) }}" wire:navigate
            {{ $attributes->class(['group block overflow-hidden rounded-lg border border-slate-100 bg-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-slate-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary']) }}>
        <div class="relative overflow-hidden">
            @if ($cover = $trip->galleryMedia()->first())
                <x-media-img :media="$cover" :alt="$cover->alt ?: $trip->title" loading="lazy" decoding="async"
                     sizes="(min-width: 1024px) 360px, (min-width: 640px) 50vw, 100vw"
                     class="h-44 w-full object-cover transition duration-300 group-hover:scale-105" />
            @else
                <div class="h-44 w-full bg-slate-100"></div>
            @endif
            @if ($trip->is_best_seller)
                <span class="absolute left-3 top-3 rounded-full bg-action px-3 py-1 text-xs font-bold tracking-wide text-white shadow">
                    BEST SELLER
                </span>
            @endif
        </div>
        <div class="p-4">
            <p class="font-semibold text-slate-900 group-hover:text-primary">{{ $trip->title }}</p>

            @if ($displayRating)
                <x-rating :rating="$displayRating" size="sm" />
            @endif

            @if ($facts)
                <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500">
                    @foreach ($facts as $fact)
                        <span class="flex items-center gap-1">
                            <x-dynamic-component :component="'heroicon-o-'.$fact['icon']" class="h-3.5 w-3.5 shrink-0 text-slate-400"/>
                            {{ $fact['value'] }}
                        </span>
                    @endforeach
                </div>
            @endif

            @if ($trip->price)
                <p class="mt-2 font-semibold text-primary">From ${{ number_format($trip->price, 0) }}</p>
            @else
                <p class="mt-2 font-semibold text-slate-500">Price on request</p>
            @endif
        </div>
    </a>
@endif

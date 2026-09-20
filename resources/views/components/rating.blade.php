@props(['rating', 'reviewsCount' => null, 'size' => 'default'])

@php
    $starSize = match ($size) {
        'sm' => 'h-3.5 w-3.5',
        'lg' => 'h-5 w-5',
        default => 'h-4 w-4',
    };
    $pillClasses = match ($size) {
        'sm' => 'px-2 py-0.5 text-[11px]',
        default => 'px-2.5 py-1 text-xs',
    };

    $rating = (float) $rating;
    $clampedRating = max(0, min(5, $rating));
@endphp

<div class="flex flex-wrap items-center gap-2">
    <div class="flex gap-0.5" aria-hidden="true">
        @for ($i = 1; $i <= 5; $i++)
            @php $fill = max(0, min(100, ($clampedRating - ($i - 1)) * 100)); @endphp
            <span class="relative inline-block {{ $starSize }}">
                <span class="absolute inset-0 text-slate-300">
                    @svg('heroicon-o-star', $starSize)
                </span>
                <span class="absolute inset-0 overflow-hidden" style="width: {{ $fill }}%">
                    <span class="absolute inset-0 text-action">
                        @svg('heroicon-s-star', $starSize)
                    </span>
                </span>
            </span>
        @endfor
    </div>

    <span class="inline-flex items-center rounded-full bg-primary font-semibold text-white {{ $pillClasses }}">
        {{ number_format($rating, 1) }} stars
    </span>

    <span class="sr-only">{{ number_format($rating, 1) }} out of 5 stars</span>

    @if ($reviewsCount)
        <span class="text-xs text-slate-500">&ndash; based on {{ $reviewsCount }} {{ Str::plural('review', $reviewsCount) }}</span>
    @endif
</div>

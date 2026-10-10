<?php

use App\Models\Trip;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component
{
    #[Locked]
    public int $tripId;

    public ?int $currentlyViewing = null;

    public ?string $lastBookedAt = null;

    public function mount(Trip $trip): void
    {
        $this->tripId = $trip->id;

        $this->refresh();
    }

    public function refresh(): void
    {
        $trip = Trip::query()->find($this->tripId, ['id', 'currently_viewing', 'last_booked_at']);

        if ($trip) {
            $trip->update(['currently_viewing' => min(80, max(20, ($trip->currently_viewing ?? 20) + random_int(-3, 3)))]);
        }

        $this->currentlyViewing = $trip?->currently_viewing;
        $this->lastBookedAt = $trip?->last_booked_at?->diffForHumans();
    }
};
?>

<div wire:poll.10s="refresh" class="flex flex-wrap items-center gap-3 text-xs font-medium text-slate-500">
    @if ($currentlyViewing)
        <span class="flex items-center gap-2 text-sm font-semibold text-secondary">
            <span class="relative flex h-2.5 w-2.5">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-secondary opacity-75"></span>
                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-secondary"></span>
            </span>
            <span
                data-count="{{ $currentlyViewing }}"
                x-data="{
                    shown: 0,
                    countTo(target) {
                        const from = this.shown;
                        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                            this.shown = target;
                            return;
                        }
                        const start = performance.now();
                        const step = (now) => {
                            const progress = Math.min((now - start) / 1500, 1);
                            this.shown = Math.round(from + (target - from) * progress * (2 - progress));
                            if (progress < 1) requestAnimationFrame(step);
                        };
                        requestAnimationFrame(step);
                    },
                }"
                x-init="
                    countTo(Number($el.dataset.count));
                    new MutationObserver(() => countTo(Number($el.dataset.count))).observe($el, { attributes: true, attributeFilter: ['data-count'] });
                "
            >
                <span wire:ignore x-text="shown" class="inline-block text-base font-bold tabular-nums">{{ $currentlyViewing }}</span>
                people currently viewing
            </span>
        </span>
    @endif
    @if ($lastBookedAt)
        <span class="text-red-500">Last booked {{ $lastBookedAt }}</span>
    @endif
</div>

@extends('frontend.layouts.app')

@section('content')
    <section class="border-b border-slate-100 bg-slate-50">
        <div class="mx-auto max-w-4xl px-6 py-16">
            @if ($trip->route)
                <a href="{{ url($trip->route->full_path) }}" wire:navigate class="text-sm font-semibold text-primary hover:text-secondary">
                    &larr; {{ $trip->title }}
                </a>
            @endif
            <h1 class="mt-4 text-4xl font-bold tracking-tight text-slate-900">Detailed Itinerary</h1>
            <p class="mt-2 text-lg text-slate-600">{{ $trip->title }} &middot; {{ count($trip->detailed_itinerary) }} days</p>
        </div>
    </section>

    <div class="mx-auto max-w-4xl px-6 py-12">
        <div class="space-y-6">
            @foreach ($trip->detailed_itinerary as $day)
                <article class="rounded-lg border border-slate-100 p-6">
                    <span class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-primary">
                        {{ $day['day'] ?? '' }}
                    </span>
                    <h2 class="mt-3 text-xl font-bold text-slate-900">{{ $day['title'] ?? '' }}</h2>

                    @php
                        $facts = array_filter([
                            'Altitude' => $day['altitude'] ?? null,
                            'Distance' => $day['distance'] ?? null,
                            'Duration' => $day['duration'] ?? null,
                            'Accommodation' => $day['accommodation'] ?? null,
                            'Meals' => $day['meals'] ?? null,
                        ]);
                    @endphp
                    @if ($facts)
                        <dl class="mt-4 grid grid-cols-2 gap-4 border-t border-slate-100 pt-4 sm:grid-cols-5">
                            @foreach ($facts as $label => $value)
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $label }}</dt>
                                    <dd class="mt-0.5 font-medium text-slate-700">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    @endif

                    @if ($day['description'] ?? null)
                        <p class="mt-4 leading-relaxed text-slate-600">{{ $day['description'] }}</p>
                    @endif
                </article>
            @endforeach
        </div>

        <div class="mt-10 flex flex-col items-start gap-4 rounded-lg bg-primary p-8 text-white sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold">Ready to book {{ $trip->title }}?</h2>
                <p class="mt-1 text-sm text-white/80">Get a tailored quote from our Nepal-based team.</p>
            </div>
            <a href="{{ route('enquiry.create', ['trip_id' => $trip->id]) }}" wire:navigate
               class="shrink-0 rounded-full bg-secondary px-6 py-3 text-center text-sm font-semibold text-white transition hover:opacity-90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                Make an Enquiry
            </a>
        </div>

        @if ($trip->route)
            <div class="mt-8">
                <a href="{{ url($trip->route->full_path) }}" wire:navigate class="font-semibold text-primary hover:opacity-80">
                    &larr; Back to {{ $trip->title }}
                </a>
            </div>
        @endif
    </div>
@endsection

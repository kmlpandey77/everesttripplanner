@extends('frontend.layouts.app')

@section('content')
    @php
        $facts = array_filter([
            'Duration' => ['icon' => 'clock', 'value' => $trip->duration],
            'Difficulty' => ['icon' => 'chart-bar', 'value' => $trip->difficulty?->getLabel()],
            'Max altitude' => ['icon' => 'arrow-trending-up', 'value' => $trip->altitude],
//            'Category' => ['icon' => 'map', 'value' => $trip->category?->name],
        ], fn (array $fact): bool => filled($fact['value']));
        $galleryImages = $trip->galleryMedia();
        $generalSettings = app(\App\Settings\GeneralSettings::class);
        $reviews = $trip->reviews()->published()->latest()->get();
        $trip->setRelation('publishedReviews', $reviews);
        $displayRating = $trip->displayRating();
        $upcomingDepartures = $trip->upcomingDepartures();
    @endphp

    {{-- Hero / Overview --}}
    <section class="relative overflow-hidden bg-white">
        <div class="mx-auto max-w-6xl px-6 py-16">
            @if ($trip->category?->route)
                <a href="{{ url($trip->category->route->full_path) }}" wire:navigate
                   class="text-sm font-medium text-primary hover:text-secondary">
                    &larr; {{ $trip->category->name }}
                </a>
            @endif

            <div class="mt-3 flex flex-wrap items-start justify-between gap-4">
                <h1 class="text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">{{ $trip->title }}</h1>

                <div class="flex shrink-0 gap-2">
                    <a href="{{ route('trip.pdf', $trip) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 rounded-full border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:border-slate-400">
                        <x-heroicon-o-printer class="h-4 w-4"/>
                        Print
                    </a>
                    <button type="button" data-share
                            data-title="{{ $trip->title }}"
                            data-url="{{ url()->current() }}"
                            class="inline-flex items-center gap-1.5 rounded-full border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:border-slate-400">
                        <x-heroicon-o-share class="h-4 w-4"/>
                        <span data-share-label>Share</span>
                    </button>
                </div>
            </div>

            <div class="mt-3 flex flex-wrap items-center gap-4">
                @if ($displayRating)
                    <x-rating :rating="$displayRating" :reviews-count="$trip->displayReviewsCount()"/>
                @endif

                <livewire:trip-stats :trip="$trip" :key="'trip-stats-'.$trip->id" />
            </div>

            <dl class="mt-6 flex flex-wrap gap-x-8 gap-y-4">
                @foreach ($facts as $label => $fact)
                    <div class="flex items-center gap-2">
                        <x-dynamic-component :component="'heroicon-o-'.$fact['icon']"
                                             class="h-6 w-6 shrink-0 text-primary"/>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-400">{{ $label }}</dt>
                            <dd class="font-semibold text-slate-900">{{ $fact['value'] }}</dd>
                        </div>
                    </div>
                @endforeach
            </dl>

            @if ($blurb = $trip->short_description ?: $trip->defaultMetaDescription())
                <p class="mt-6 text-lg text-slate-600 border-l-4 rounded-lg border-primary p-3 bg-gray-100">
{{--                    <span class="mr-2 inline-block align-top text-4xl text-slate-400"> @svg('heroicon-s-chat-bubble-left-ellipsis') </span>--}}
                    {{ $blurb }}
                </p>
            @endif

            @if ($galleryImages->isNotEmpty())
                <div class="trip-gallery mt-8">
                    <div class="swiper trip-gallery-swiper relative overflow-hidden rounded-2xl">
                        <div class="swiper-wrapper">
                            @foreach ($galleryImages as $image)
                                <div class="swiper-slide">
                                    <img src="{{ $image->url }}" alt="{{ $image->alt ?: $trip->title }}"
                                         class="h-72 w-full object-cover sm:h-[28rem]">
                                </div>
                            @endforeach
                        </div>

                        @if ($galleryImages->count() > 1)
                            <button type="button"
                                    class="trip-gallery-prev absolute left-4 top-1/2 z-10 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-slate-700 shadow hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
                                    aria-label="Previous image">
                                <x-heroicon-o-chevron-left class="h-5 w-5"/>
                            </button>
                            <button type="button"
                                    class="trip-gallery-next absolute right-4 top-1/2 z-10 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-slate-700 shadow hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
                                    aria-label="Next image">
                                <x-heroicon-o-chevron-right class="h-5 w-5"/>
                            </button>
                        @endif
                    </div>

                    @if ($galleryImages->count() > 1)
                        <div class="trip-gallery-pagination mt-4 flex justify-center gap-2"></div>
                    @endif
                </div>
            @endif
        </div>
    </section>

    {{-- Sticky section nav --}}
    <nav class="trip-section-nav sticky top-0 z-10 border-b border-slate-200 bg-white/95 backdrop-blur bg-linear-to-b from-slate-200 to-white">
        <div class="mx-auto flex max-w-6xl gap-6 overflow-x-auto px-6 text-sm font-medium text-slate-600">
            @foreach (array_filter([
                'highlights' => $trip->highlights ? 'Highlights' : null,
                'departures' => $upcomingDepartures->isNotEmpty() ? 'Departure Dates' : null,
                'itinerary' => $trip->detailed_itinerary ? 'Itinerary' : null,
                'trip-details' => $trip->overview ? 'Trip details' : null,
                'includes' => $trip->includes ? 'Includes' : null,
                'excludes' => $trip->excludes ? 'Excludes' : null,
                'practical-info' => ($trip->checklist || $trip->know_before_you_go || $trip->do_not_do) ? 'Practical Info' : null,
                'map' => $trip->map_url ? 'Map' : null,
                'reviews' => $reviews->isNotEmpty() ? 'Reviews' : null,
                'faq' => $trip->faq ? 'FAQs' : null,
            ]) as $anchor => $label)
                <a href="#{{ $anchor }}"
                   class="whitespace-nowrap border-b-2 border-transparent py-4 hover:border-primary hover:text-primary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </nav>

    <div class="mx-auto max-w-6xl px-6 py-12">
        <div class="grid gap-12 lg:grid-cols-3">
            <div class="lg:col-span-2">
                {{-- Highlights --}}
                @if ($trip->highlights)
                    <section id="highlights" class="scroll-mt-20">
                        <h2 class="text-2xl font-bold text-slate-900">Highlights</h2>
                        <ul class="mt-5 space-y-4">
                            @foreach ($trip->highlights as $highlight)
                                <li class="flex items-start gap-3">
                                    <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-secondary/10 text-secondary">
                                        @svg('heroicon-o-check', 'h-4 w-4')
                                    </span>
                                    <span class="text-base leading-relaxed text-slate-700">{{ $highlight }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                {{-- Departure Dates --}}
                @if ($upcomingDepartures->isNotEmpty())
                    <section id="departures" class="mt-12 scroll-mt-20">
                        <h2 class="text-2xl font-bold text-slate-900">Departure Dates</h2>
                        <div class="mt-6 overflow-hidden rounded-lg border border-slate-100">
                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[520px] text-left text-sm">
                                    <thead>
                                    <tr class="border-b border-slate-100 bg-slate-50">
                                        <th scope="col" class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Date</th>
                                        <th scope="col" class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Price</th>
                                        <th scope="col" class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Status</th>
                                        <th scope="col" class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400"><span class="sr-only">Actions</span></th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                    @foreach ($upcomingDepartures as $departure)
                                        @php
                                            $seats = filled($departure['seats_available'] ?? null) ? (int) $departure['seats_available'] : null;
                                            $soldOut = $seats !== null && $seats <= 0;
                                            $fewLeft = $seats !== null && $seats > 0 && $seats <= 5;
                                        @endphp
                                        <tr class="odd:bg-slate-50">
                                            <td class="whitespace-nowrap px-4 py-3 align-top font-semibold text-slate-900">
                                                {{ \Illuminate\Support\Carbon::parse($departure['date'])->format('d M Y') }}
                                                @if ($departure['is_guaranteed'] ?? false)
                                                    <span class="ml-2 inline-flex items-center rounded-full bg-secondary/10 px-2 py-0.5 text-[11px] font-semibold text-secondary">Guaranteed</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 align-top text-slate-700">
                                                ${{ number_format((float) ($departure['price'] ?: $trip->price ?: 0), 0) }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 align-top">
                                                @if ($soldOut)
                                                    <span class="text-slate-400">Sold out</span>
                                                @elseif ($fewLeft)
                                                    <span class="font-semibold text-slate-900">Only {{ $seats }} seats left</span>
                                                @elseif ($seats !== null)
                                                    <span class="text-secondary">{{ $seats }} seats open</span>
                                                @else
                                                    <span class="text-secondary">Open</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 align-top text-right">
                                                @unless ($soldOut)
                                                    <a href="{{ route('enquiry.create', ['trip_id' => $trip->id]) }}" wire:navigate
                                                       class="inline-flex items-center rounded-full bg-secondary px-4 py-1.5 text-xs font-semibold text-white hover:opacity-90">
                                                        Book
                                                    </a>
                                                @endunless
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                @endif

                {{-- Itinerary --}}
                @if ($trip->detailed_itinerary)
                    <section id="itinerary" class="mt-12 scroll-mt-20">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <h2 class="text-2xl font-bold text-slate-900">Itinerary</h2>
                            @if ($trip->itineraryRoute)
                                <a href="{{ url($trip->itineraryRoute->full_path) }}" wire:navigate
                                   class="text-sm font-semibold text-primary hover:opacity-80">
                                    View detailed itinerary &rarr;
                                </a>
                            @endif
                        </div>
                        <div class="mt-6 overflow-hidden rounded-lg border border-slate-100">
                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[560px] text-left text-sm">
                                    <thead>
                                    <tr class="border-b border-slate-100 bg-slate-50">
                                        <th scope="col"
                                            class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Day
                                        </th>
                                        <th scope="col"
                                            class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Activity
                                        </th>
                                        <th scope="col"
                                            class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Walking Hr
                                        </th>
                                        <th scope="col"
                                            class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Max Altitude
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                    @foreach ($trip->detailed_itinerary as $day)
                                        <tr class="odd:bg-slate-100">
                                            <td class="whitespace-nowrap px-4 py-2 align-top font-semibold text-slate-900">{{ $day['day'] ?? '' }}</td>
                                            <td class="px-4 py-2 align-top text-slate-700">{{ $day['title'] ?? '' }}</td>
                                            <td class="whitespace-nowrap px-4 py-2 align-top text-slate-600">{{ $day['duration'] ?? '—' }}</td>
                                            <td class="whitespace-nowrap px-4 py-2 align-top text-slate-600">{{ $day['altitude'] ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @unless ($trip->itineraryRoute)
                            <div class="mt-6 space-y-3">
                                @foreach ($trip->detailed_itinerary as $day)
                                    @if ($day['description'] ?? null)
                                        <p class="text-slate-600"><span class="font-semibold text-slate-900">{{ $day['day'] ?? '' }}:</span> {{ $day['description'] }}
                                        </p>
                                    @endif
                                @endforeach
                            </div>
                        @endunless
                    </section>
                @endif

                {{-- Trip details (overview) --}}
                @if ($trip->overview)
                    <section id="trip-details" class="mt-12 scroll-mt-20">
                        <h2 class="text-2xl font-bold text-slate-900">Trip details</h2>
                        <div class="prose prose-slate mt-4 max-w-none prose-a:text-primary prose-a:font-semibold prose-a:no-underline hover:prose-a:text-secondary hover:prose-a:underline">
                            {!! $trip->overview !!}
                        </div>
                    </section>
                @endif

                {{-- Includes --}}
                @if ($trip->includes)
                    <section id="includes" class="mt-12 scroll-mt-20">
                        <h2 class="text-2xl font-bold text-slate-900">Includes</h2>
                        <ul class="mt-4 grid gap-2 sm:grid-cols-2">
                            @foreach ($trip->includes as $item)
                                <li class="flex items-start gap-2 text-slate-600">
                                    <span class="mt-0.5 shrink-0 text-secondary">@svg('heroicon-o-check', 'h-4 w-4')</span>
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                {{-- Excludes --}}
                @if ($trip->excludes)
                    <section id="excludes" class="mt-12 scroll-mt-20">
                        <h2 class="text-2xl font-bold text-slate-900">Excludes</h2>
                        <ul class="mt-4 grid gap-2 sm:grid-cols-2">
                            @foreach ($trip->excludes as $item)
                                <li class="flex items-start gap-2 text-slate-600">
                                    <span class="mt-0.5 shrink-0 text-slate-400">@svg('heroicon-o-x-mark', 'h-4 w-4')</span>
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                {{-- Practical Info --}}
                @if ($trip->checklist || $trip->know_before_you_go || $trip->do_not_do)
                    <section id="practical-info" class="mt-12 scroll-mt-20">
                        <h2 class="text-2xl font-bold text-slate-900">Practical Info</h2>

                        @if ($trip->know_before_you_go)
                            <div class="prose prose-slate mt-4 max-w-none prose-a:text-primary prose-a:font-semibold prose-a:no-underline hover:prose-a:text-secondary hover:prose-a:underline">
                                {!! $trip->know_before_you_go !!}
                            </div>
                        @endif

                        <div class="mt-6 grid gap-8 sm:grid-cols-2">
                            @if ($trip->checklist)
                                <div>
                                    <h3 class="font-semibold text-slate-900">Packing Checklist</h3>
                                    <ul class="mt-3 space-y-2 text-slate-600">
                                        @foreach ($trip->checklist as $item)
                                            <li class="flex items-start gap-2">
                                                <span class="mt-0.5 shrink-0 text-secondary">@svg('heroicon-o-check', 'h-4 w-4')</span>
                                                {{ $item }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if ($trip->do_not_do)
                                <div>
                                    <h3 class="font-semibold text-slate-900">Do Not Do</h3>
                                    <ul class="mt-3 space-y-2 text-slate-600">
                                        @foreach ($trip->do_not_do as $item)
                                            <li class="flex items-start gap-2">
                                                <span class="mt-0.5 shrink-0 text-action">@svg('heroicon-o-x-mark', 'h-4 w-4')</span>
                                                {{ $item }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </section>
                @endif

                {{-- Map --}}
                @if ($trip->map_url)
                    <section id="map" class="mt-12 scroll-mt-20">
                        <h2 class="text-2xl font-bold text-slate-900">Route Map</h2>
                        <iframe src="{{ $trip->map_url }}" class="mt-4 h-96 w-full rounded-lg border border-slate-100"
                                loading="lazy"></iframe>
                    </section>
                @endif

                {{-- Reviews --}}
                @if ($reviews->isNotEmpty())
                    <section id="reviews" class="mt-12 scroll-mt-20">
                        <h2 class="text-2xl font-bold text-slate-900">Customer Reviews</h2>
                        <div class="mt-6 grid gap-2">
                            @foreach ($reviews as $review)
                                @php $avatar = \Awcodes\Curator\Models\Media::find($review->avatar_id); @endphp
                                <figure class="rounded-lg border border-slate-100 bg-white p-6">
                                    <x-rating :rating="$review->rating" size="sm" />
                                    <blockquote class="mt-3 text-slate-600">{{ $review->quote }}</blockquote>
                                    <figcaption class="mt-4 flex items-center gap-3 border-t border-slate-100 pt-4">
                                        @if ($avatar)
                                            <img src="{{ $avatar->url }}" alt="" class="h-10 w-10 shrink-0 rounded-full object-cover">
                                        @else
                                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 font-semibold text-slate-500">
                                                {{ Str::of($review->reviewer_name)->substr(0, 1)->upper() }}
                                            </span>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ $review->reviewer_name }}</p>
                                            @if ($review->reviewer_role)
                                                <p class="text-sm text-slate-500">{{ $review->reviewer_role }}</p>
                                            @endif
                                        </div>
                                    </figcaption>
                                </figure>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- FAQ --}}
                @if ($trip->faq)
                    <section id="faq" class="mt-12 scroll-mt-20">
                        <h2 class="text-2xl font-bold text-slate-900">Frequently Asked Questions</h2>
                        <div class="mt-4 divide-y divide-slate-100 rounded-lg border border-slate-100">
                            @foreach ($trip->faq as $entry)
                                <details class="group p-4">
                                    <summary
                                            class="flex list-none cursor-pointer items-center justify-between gap-4 rounded-md font-semibold text-slate-900 marker:content-none focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary [&::-webkit-details-marker]:hidden">
                                        <span>{{ $entry['question'] ?? '' }}</span>
                                        @svg('heroicon-o-chevron-down', 'h-5 w-5 shrink-0 text-slate-400 transition duration-200 group-open:rotate-180')
                                    </summary>
                                    <p class="mt-3 text-slate-600">{{ $entry['answer'] ?? '' }}</p>
                                </details>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            {{-- Sidebar --}}
            <aside class="lg:col-span-1">
                <div class="relative sticky top-24 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    @if ($trip->is_best_seller)
                        <span class="absolute -top-3 right-4 rounded-full bg-action px-3 py-1 text-xs font-bold tracking-wide text-white shadow">
                            BEST SELLER
                        </span>
                    @endif

                    @if ($trip->price)
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">From</p>
                        <p class="mt-1 flex items-baseline gap-2">
                            <span class="text-3xl font-bold text-slate-900">${{ number_format($trip->price, 0) }}</span>
                            <span class="text-sm text-slate-500">/ person</span>
                        </p>
                        @if ($trip->duration)
                            <p class="mt-1 text-sm text-slate-500">{{ $trip->duration }} trip</p>
                        @endif
                    @else
                        <dl class="space-y-2 text-sm">
                            @foreach ($facts as $label => $fact)
                                <div class="flex justify-between border-b border-slate-100 pb-2">
                                    <dt class="text-slate-500">{{ $label }}</dt>
                                    <dd class="font-medium text-slate-900">{{ $fact['value'] }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    @endif

                    @if ($trip->price_tiers)
                        <div class="mt-4 overflow-hidden rounded-lg border border-slate-100">
                            <p class="border-b border-slate-100 bg-slate-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Group Discount
                            </p>
                            @foreach ($trip->price_tiers as $tier)
                                <div class="flex justify-between px-3 py-2 text-sm odd:bg-slate-50">
                                    <span class="text-slate-600">{{ $tier['label'] ?? '' }}</span>
                                    <span class="font-semibold text-slate-900">${{ number_format((float) ($tier['price'] ?? 0), 0) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-6 space-y-3">
                        <a href="{{ route('plan-trip.create', ['trip_id' => $trip->id]) }}" wire:navigate
                           class="block rounded-full bg-secondary px-6 py-3 text-center text-sm font-semibold text-white hover:opacity-90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                            Book Now
                        </a>
                        <a href="{{ route('enquiry.create', ['trip_id' => $trip->id]) }}" wire:navigate
                           class="block rounded-full border border-slate-300 px-6 py-3 text-center text-sm font-semibold text-slate-700 hover:border-slate-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                            Make an Enquiry
                        </a>
                    </div>

                    <ul class="mt-6 space-y-2 border-t border-slate-100 pt-6 text-sm text-slate-600">
                        @foreach ([
                            'Local guides based in the region you\'re visiting',
                            'Structured acclimatization for safety',
                            'Transparent pricing with no surprise costs',
                            'Flexible dates if your plans change',
                        ] as $trustItem)
                            <li class="flex items-start gap-2">
                                <span class="mt-0.5 shrink-0 text-secondary">@svg('heroicon-o-check', 'h-4 w-4')</span>
                                {{ $trustItem }}
                            </li>
                        @endforeach
                    </ul>

                    @if ($generalSettings->phone || $generalSettings->whatsapp_number)
                        <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-6">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Instant Help</p>
                                <p class="text-sm text-slate-600">Call or WhatsApp</p>
                            </div>
                            <div class="flex gap-2">
                                @if ($generalSettings->phone)
                                    <a href="tel:{{ preg_replace('/[^+0-9]/', '', $generalSettings->phone) }}" aria-label="Call {{ $generalSettings->phone }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 text-slate-600 hover:border-slate-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                                        <x-heroicon-o-phone class="h-5 w-5" />
                                    </a>
                                @endif
                                @if ($generalSettings->whatsapp_number)
                                    <a href="https://wa.me/{{ $generalSettings->whatsapp_number }}" target="_blank" rel="noopener" aria-label="Message on WhatsApp" class="flex h-11 w-11 items-center justify-center rounded-full bg-secondary text-white hover:opacity-90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                                        <x-heroicon-o-chat-bubble-left-right class="h-5 w-5" />
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </aside>
        </div>

        {{-- Related trips --}}
        @php $related = $trip->relatedTrips(); @endphp
        @if ($related->isNotEmpty())
            <section class="mt-16 border-t border-slate-100 pt-12">
                <h2 class="text-2xl font-bold text-slate-900">You might also like</h2>
                <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $relatedTrip)
                        <x-trip-card :trip="$relatedTrip"/>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection

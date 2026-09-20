@php $items = $data['items'] ?? []; @endphp
@if (! empty($items))
    <section class="mx-auto max-w-6xl px-6 py-16">
        @if ($data['heading'] ?? null)
            <h2 class="text-center text-3xl font-bold tracking-tight text-slate-900">{{ $data['heading'] }}</h2>
        @endif
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($items as $item)
                @continue(blank($item['quote'] ?? null) || blank($item['name'] ?? null))
                @php $avatar = \Awcodes\Curator\Models\Media::find($item['avatar_id'] ?? null); @endphp
                <figure class="rounded-lg border border-slate-100 bg-white p-6">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-primary/10 text-primary">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                            <rect x="5" y="6" width="4" height="8" rx="2" />
                            <rect x="15" y="6" width="4" height="8" rx="2" />
                        </svg>
                    </span>

                    <blockquote class="mt-4 text-slate-600">{{ $item['quote'] }}</blockquote>

                    @if ($rating = (int) ($item['rating'] ?? 0))
                        <div class="mt-4 flex gap-0.5" aria-label="{{ $rating }} out of 5 stars">
                            @for ($i = 1; $i <= 5; $i++)
                                @svg($i <= $rating ? 'heroicon-s-star' : 'heroicon-o-star', 'h-4 w-4 '.($i <= $rating ? 'text-slate-900' : 'text-slate-300'))
                            @endfor
                        </div>
                    @endif

                    <figcaption class="mt-4 flex items-center gap-3 border-t border-slate-100 pt-4">
                        @if ($avatar)
                            <img src="{{ $avatar->url }}" alt="" class="h-10 w-10 shrink-0 rounded-full object-cover">
                        @else
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 font-semibold text-slate-500">
                                {{ Str::of($item['name'])->substr(0, 1)->upper() }}
                            </span>
                        @endif
                        <div>
                            <p class="font-semibold text-slate-900">{{ $item['name'] }}</p>
                            @if ($item['role'] ?? null)
                                <p class="text-sm text-slate-500">{{ $item['role'] }}</p>
                            @endif
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </section>
@endif

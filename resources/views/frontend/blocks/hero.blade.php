@php $media = \Awcodes\Curator\Models\Media::find($data['media_id'] ?? null); @endphp
<section class="relative overflow-hidden bg-slate-900 text-white">
    @if ($media)
        <x-media-img
            :media="$media"
            :alt="$media->alt ?: ($data['heading'] ?? '')"
            class="absolute inset-0 h-full w-full object-cover"
            fetchpriority="high"
        />
        <div class="absolute inset-0 bg-black/45"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/10 to-transparent"></div>
    @endif
    <div class="relative mx-auto max-w-4xl px-6 pt-40 pb-24 text-center sm:pt-48 sm:pb-28">
        <h1 class="hero-enter text-balance text-4xl font-bold tracking-tight sm:text-5xl">{{ $data['heading'] ?? '' }}</h1>
        @if ($data['subheading'] ?? null)
            <p class="hero-enter hero-enter-2 text-balance mx-auto mt-4 max-w-2xl text-lg text-slate-200">{{ $data['subheading'] }}</p>
        @endif
        <div class="hero-enter hero-enter-3 mt-8 flex flex-wrap items-center justify-center gap-4">
            @if ($data['primary_button_url'] ?? null)
                <a href="{{ $data['primary_button_url'] }}" wire:navigate
                   class="rounded-full bg-gradient-to-b from-primary to-secondary px-6 py-3 font-semibold tracking-wide text-white shadow-lg shadow-black/30 transition hover:shadow-xl hover:shadow-black/40 md:px-8">
                    {{ $data['primary_button_label'] ?? 'Learn more' }}
                </a>
            @endif
            @if ($data['secondary_button_url'] ?? null)
                <a href="{{ $data['secondary_button_url'] }}" wire:navigate
                   class="rounded-full border border-white/30 px-6 py-3 font-semibold text-white transition hover:border-white/60 hover:bg-white/10">
                    {{ $data['secondary_button_label'] ?? 'Learn more' }}
                </a>
            @endif
        </div>
    </div>
</section>

@php
    $posts = \App\Models\Blog::query()
        ->with(['route', 'image'])
        ->where('status', \App\Enums\PageStatus::Published)
        ->where(fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()))
        ->latest('published_at')
        ->paginate((int) ($data['count'] ?? 12));
@endphp
@if ($posts->isNotEmpty())
    <div class="mx-auto grid max-w-6xl gap-6 px-6 py-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($posts as $post)
            <a href="{{ $post->route ? url($post->route->full_path) : '#' }}" wire:navigate
               class="group overflow-hidden rounded-lg border border-slate-100 bg-white transition hover:shadow-md">
                @if ($post->image)
                    <x-media-img :media="$post->image" :alt="$post->image->alt ?: $post->title" sizes="(min-width: 1024px) 360px, (min-width: 640px) 50vw, 100vw" loading="lazy" decoding="async" class="aspect-video w-full object-cover" />
                @endif
                <div class="p-5">
                    @if ($post->published_at)
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $post->published_at->format('F j, Y') }}</p>
                    @endif
                    <h3 class="mt-1 font-semibold text-slate-900 group-hover:text-primary">{{ $post->title }}</h3>
                    @if ($post->excerpt)
                        <p class="mt-2 text-sm text-slate-600">{{ $post->excerpt }}</p>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
    @if ($posts->hasPages())
        <div class="mx-auto max-w-6xl px-6 py-6">
            {{ $posts->links() }}
        </div>
    @endif
@endif

@php
    $media = ($data['media_id'] ?? null) ? \Awcodes\Curator\Models\Media::find($data['media_id']) : null;
    $direction = match ($data['image_position'] ?? 'top') {
        'left' => 'flex-col sm:flex-row sm:items-center',
        'right' => 'flex-col sm:flex-row-reverse sm:items-center',
        'bottom' => 'flex-col-reverse',
        default => 'flex-col',
    };
    $fit = match ($data['image_fit'] ?? 'cover') {
        'fit' => 'aspect-video w-full object-contain',
        'full' => 'w-full h-auto',
        default => 'aspect-video w-full object-cover',
    };
    $alignClass = match ($data['align'] ?? 'left') {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };
@endphp
<div @class(['mx-auto max-w-6xl' => ($data['width'] ?? 'container') !== 'full', 'px-6 py-4'])>
    @if ($media)
        <div class="flex {{ $direction }} gap-6">
            <div class="sm:w-2/5 sm:shrink-0">
                <x-media-img :media="$media" :alt="$data['alt'] ?? $media->alt" sizes="(min-width: 640px) 440px, 100vw" loading="lazy" decoding="async" class="{{ $fit }} rounded-lg border border-slate-100 shadow-sm" />
            </div>
            <div class="prose prose-slate max-w-none flex-1 {{ $alignClass }} prose-a:font-semibold prose-a:text-primary prose-a:no-underline hover:prose-a:text-secondary hover:prose-a:underline">
                {!! $data['content'] ?? '' !!}
            </div>
        </div>
    @else
        <div class="prose prose-slate mx-auto max-w-none {{ $alignClass }} prose-a:font-semibold prose-a:text-primary prose-a:no-underline hover:prose-a:text-secondary hover:prose-a:underline">
            {!! $data['content'] ?? '' !!}
        </div>
    @endif
</div>

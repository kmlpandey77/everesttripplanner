@props(['media', 'sizes' => '100vw'])

@php
    // ponytail: resized on the fly by Curator's Glide route and cached on disk; pre-generate variants if image requests show up in CPU profiles.
    $widths = collect([320, 480, 768, 1024, 1440, 1920])
        ->filter(fn (int $width): bool => ! $media->width || $width < $media->width)
        ->when($media->width, fn ($widths) => $widths->push($media->width));

    $srcset = $widths
        ->map(fn (int $width): string => \Awcodes\Curator\Glide\GlideBuilder::make()->width($width)->format('webp')->toUrl($media->path)." {$width}w")
        ->implode(', ');
@endphp

<img src="{{ $media->url }}"
     srcset="{{ $srcset }}"
     sizes="{{ $sizes }}"
     @if ($media->width && $media->height) width="{{ $media->width }}" height="{{ $media->height }}" @endif
     {{ $attributes }}>

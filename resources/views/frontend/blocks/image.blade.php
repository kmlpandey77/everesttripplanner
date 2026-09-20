@if ($media = \Awcodes\Curator\Models\Media::find($data['media_id'] ?? null))
    <div class="mx-auto max-w-3xl px-6 py-4">
        <img src="{{ $media->url }}" alt="{{ $data['alt'] ?: $media->alt }}" class="aspect-video w-full rounded-lg border border-slate-100 object-cover shadow-sm">
    </div>
@endif

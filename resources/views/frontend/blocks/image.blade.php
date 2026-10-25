@if ($media = \Awcodes\Curator\Models\Media::find($data['media_id'] ?? null))
    <div class="mx-auto max-w-3xl px-6 py-4">
        <x-media-img :media="$media" :alt="$data['alt'] ?: $media->alt" sizes="(min-width: 768px) 720px, 100vw" loading="lazy" decoding="async" class="aspect-video w-full rounded-lg border border-slate-100 object-cover shadow-sm" />
    </div>
@endif

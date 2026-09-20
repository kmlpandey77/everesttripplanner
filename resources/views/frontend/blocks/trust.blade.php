<section class="border-y border-slate-100 bg-slate-50">
    <div class="mx-auto max-w-6xl px-6 py-5">
        <ul class="flex flex-col divide-y divide-slate-200 sm:flex-row sm:flex-wrap sm:items-center sm:justify-center sm:gap-x-10 sm:divide-x sm:divide-y-0">
            @foreach ($data['items'] ?? [] as $item)
                <li class="flex items-center gap-2.5 py-3 first:pt-0 last:pb-0 sm:py-0 sm:pl-6 sm:first:pl-0">
                    @svg($item['icon'] ?? 'heroicon-o-check-badge', 'h-5 w-5 shrink-0 text-primary')
                    <span class="text-sm font-medium text-slate-600">{{ $item['label'] ?? '' }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</section>

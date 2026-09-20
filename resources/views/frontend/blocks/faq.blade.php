<section class="mx-auto max-w-3xl px-6 py-16">
    @if ($data['heading'] ?? null)
        <h2 class="text-3xl font-bold tracking-tight text-slate-900">{{ $data['heading'] }}</h2>
    @endif
    <div class="mt-6 divide-y divide-slate-100 rounded-lg border border-slate-100">
        @foreach ($data['items'] ?? [] as $item)
            <details class="group p-4">
                <summary class="flex list-none cursor-pointer items-center justify-between gap-4 rounded-md font-semibold text-slate-900 marker:content-none focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary [&::-webkit-details-marker]:hidden">
                    <span>{{ $item['question'] ?? '' }}</span>
                    @svg('heroicon-o-chevron-down', 'h-5 w-5 shrink-0 text-slate-400 transition duration-200 group-open:rotate-180')
                </summary>
                <p class="mt-3 text-slate-600">{{ $item['answer'] ?? '' }}</p>
            </details>
        @endforeach
    </div>
</section>

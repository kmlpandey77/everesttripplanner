<aside class="mt-10 lg:sticky lg:top-24 lg:mt-0">
    <nav class="rounded-lg border border-slate-100 bg-slate-50 p-5">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">In this section</p>
        <ul class="mt-4 space-y-2 text-sm">
            @foreach ($siblingPages as $sibling)
                <li>
                    <a
                            href="{{ url($sibling->full_path) }}"
                            wire:navigate
                            class="{{ $sibling->routable_id === $page->id ? 'font-semibold text-primary' : 'text-slate-600 hover:text-primary' }}"
                    >
                        {{ $sibling->routable->title }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</aside>

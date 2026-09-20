@extends('frontend.layouts.app')

@section('content')
    @php
        $blocks = collect($page->content ?? []);
        $heroBlocks = $blocks->filter(fn ($block) => ($block['type'] ?? null) === 'hero');
        $hasHero = $heroBlocks->isNotEmpty();
        $contentBlocks = $blocks->reject(fn ($block) => ($block['type'] ?? null) === 'hero')->values();
        $siblingPages = $siblingPages ?? collect();
    @endphp

    {{-- Hero blocks are full-bleed backgrounds, so they render outside the sidebar grid. --}}
    @foreach ($heroBlocks as $block)
        @includeFirst(
            ["frontend.blocks.{$block['type']}", 'frontend.blocks.missing'],
            ['data' => $block['data'] ?? []]
        )
    @endforeach

    @unless ($hasHero)
        <section class="border-b border-slate-100 bg-slate-50">
            <div class="mx-auto max-w-6xl px-6 py-8">
                <h1 class="text-4xl font-bold tracking-tight text-slate-900">{{ $page->title }}</h1>
            </div>
        </section>
    @endunless

    <div @class([
        'mx-auto max-w-6xl lg:grid lg:grid-cols-[1fr_260px] lg:items-start lg:gap-10' => $siblingPages->isNotEmpty(),
    ])>
        <div class="min-w-0">
            @foreach ($contentBlocks as $index => $block)
                {{-- The hero and the band above already render the page's one h1; a leading heading:h1 block would duplicate it. --}}
                @continue($index === 0 && ($block['type'] ?? null) === 'heading' && ($block['data']['level'] ?? null) === 'h1')

                @includeFirst(
                    ["frontend.blocks.{$block['type']}", 'frontend.blocks.missing'],
                    ['data' => $block['data'] ?? []]
                )
            @endforeach
        </div>

        @if ($siblingPages->isNotEmpty())
            @include('frontend.partials.section-nav', ['page' => $page, 'siblingPages' => $siblingPages])
        @endif
    </div>
@endsection

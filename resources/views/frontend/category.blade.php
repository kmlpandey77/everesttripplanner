@extends('frontend.layouts.app')

@section('content')
    @php $trips = $category->trips->filter->isPublished(); @endphp

    <section class="border-b border-slate-100 bg-slate-50">
        <div class="mx-auto max-w-6xl px-6 py-16">
            <h1 class="text-4xl font-bold tracking-tight text-slate-900">{{ $category->name }}</h1>
            @if ($category->description)
                <p class="mt-4 max-w-2xl text-lg text-slate-600">{{ $category->description }}</p>
            @endif
        </div>
    </section>

    <div class="mx-auto max-w-6xl px-6 py-12">
        @if ($category->children->isNotEmpty())
            <section>
                <h2 class="text-xl font-bold text-slate-900">Subcategories</h2>
                <div class="mt-4 flex flex-wrap gap-3">
                    @foreach ($category->children as $child)
                        @if ($child->route)
                            <a href="{{ url($child->route->full_path) }}" wire:navigate
                               class="rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:border-primary hover:text-primary">
                                {{ $child->name }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif

        @if ($trips->isNotEmpty())
            <section class="mt-12">
                <h2 class="text-xl font-bold text-slate-900">Trips</h2>
                <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($trips as $trip)
                        <x-trip-card :trip="$trip" />
                    @endforeach
                </div>
            </section>
        @endif

        @if ($category->children->isEmpty() && $trips->isEmpty())
            <p class="text-slate-500">Nothing published here yet.</p>
        @endif
    </div>
@endsection

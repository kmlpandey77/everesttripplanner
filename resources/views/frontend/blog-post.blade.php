@extends('frontend.layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl px-6 py-16">
        <a href="{{ url('blogs') }}" wire:navigate class="text-sm font-semibold text-primary hover:text-secondary">
            &larr; Blogs
        </a>

        <h1 class="mt-4 text-4xl font-bold tracking-tight text-slate-900">{{ $blog->title }}</h1>

        @if ($blog->published_at)
            <p class="mt-2 text-sm text-slate-500">{{ $blog->published_at->format('F j, Y') }}</p>
        @endif

        @if ($blog->image)
            <img src="{{ $blog->image->url }}" alt="{{ $blog->image->alt ?: $blog->title }}"
                 class="mt-8 aspect-video w-full rounded-lg border border-slate-100 object-cover shadow-sm">
        @endif

        <div class="prose prose-slate mt-8 max-w-none prose-a:font-semibold prose-a:text-primary prose-a:no-underline hover:prose-a:text-secondary hover:prose-a:underline">
            {!! $blog->content !!}
        </div>
    </div>
@endsection

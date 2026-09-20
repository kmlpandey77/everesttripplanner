@php
    $level = $data['level'] ?? 'h2';
    $sizes = ['h1' => 'text-4xl', 'h2' => 'text-3xl', 'h3' => 'text-2xl', 'h4' => 'text-xl'];
    $alignClass = match ($data['align'] ?? 'left') {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };
@endphp
<div @class(['mx-auto max-w-6xl' => ($data['width'] ?? 'container') !== 'full', 'px-6 pt-10'])>
    <{{ $level }} class="font-bold tracking-tight text-slate-900 {{ $sizes[$level] ?? 'text-2xl' }} {{ $alignClass }}">{{ $data['text'] ?? '' }}</{{ $level }}>
</div>

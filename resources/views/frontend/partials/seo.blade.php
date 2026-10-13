<title>{{ $seo['title'] }}</title>
@if ($seo['description'])
    <meta name="description" content="{{ $seo['description'] }}">
@endif
<meta name="robots" content="{{ $seo['robots'] }}">
<link rel="canonical" href="{{ $seo['canonical'] }}">

<meta property="og:type" content="{{ $seo['og_type'] ?? 'website' }}">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:locale" content="en_US">
<meta property="og:title" content="{{ $seo['og_title'] }}">
@if ($seo['og_description'])
    <meta property="og:description" content="{{ $seo['og_description'] }}">
@endif
<meta property="og:image" content="{{ $seo['og_image'] ?: asset('images/logo.webp') }}">
<meta property="og:image:alt" content="{{ $seo['og_title'] }}">
<meta property="og:url" content="{{ $seo['canonical'] }}">

@if ($seo['twitter_card'])
    <meta name="twitter:card" content="{{ $seo['twitter_card'] }}">
    <meta name="twitter:title" content="{{ $seo['twitter_title'] }}">
    @if ($seo['twitter_description'])
        <meta name="twitter:description" content="{{ $seo['twitter_description'] }}">
    @endif
    @if ($seo['twitter_image'])
        <meta name="twitter:image" content="{{ $seo['twitter_image'] }}">
    @endif
@endif

<script type="application/ld+json">{!! $seo['schema_ld'] !!}</script>

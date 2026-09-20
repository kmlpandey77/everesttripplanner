<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @isset($seo)
        @include('frontend.partials.seo')
    @endisset
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex min-h-screen flex-col bg-white text-slate-800 antialiased">
@php
    $mainMenu = \App\Models\Menu::where('slug', 'main')->with('items.children')->first();
    $generalSettings = app(\App\Settings\GeneralSettings::class);
@endphp
<header class="border-b border-slate-200 bg-linear-to-b from-slate-200 to-white" x-data="{ mobileOpen: false }" @keydown.escape.window="mobileOpen = false">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-6 px-6 py-5">
        <a href="{{ url('/') }}" wire:navigate class="shrink-0" @click="mobileOpen = false">
            <img src="{{ asset('images/logo.webp') }}" alt="{{ config('app.name') }}" class="h-10 w-auto">
        </a>

        @if ($mainMenu)
            <nav class="hidden items-center gap-6 text-sm font-medium text-slate-600 md:flex">
                @foreach ($mainMenu->items as $item)
                    @if ($item->children->isEmpty())
                        <a
                                href="{{ $item->url }}"
                                @if ($item->open_in_new_tab) target="_blank" rel="noopener" @else wire:navigate @endif
                                class="hover:text-primary"
                        >
                            {{ $item->label }}
                        </a>
                    @else
                        <div class="group relative">
                            <button type="button" class="flex items-center gap-1 hover:text-primary">
                                {{ $item->label }}
                                <svg viewBox="0 0 20 20" fill="currentColor"
                                     class="h-4 w-4 transition group-hover:rotate-180">
                                    <path fill-rule="evenodd"
                                          d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <div class="invisible absolute left-0 top-full z-10 min-w-48 rounded-lg border border-slate-100 bg-white py-2 opacity-0 shadow-lg transition group-hover:visible group-hover:opacity-100">
                                @foreach ($item->children as $child)
                                    <a
                                            href="{{ $child->url }}"
                                            @if ($child->open_in_new_tab) target="_blank" rel="noopener"
                                            @else wire:navigate @endif
                                            class="block px-4 py-2 hover:bg-slate-50 hover:text-primary"
                                    >
                                        {{ $child->label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>
        @endif

        <div class="flex shrink-0 items-center gap-3">
            @if ($mainMenu?->cta_label && $mainMenu?->cta_url)
                <a
                        href="{{ $mainMenu->cta_url }}"
                        wire:navigate
                        class="hidden shrink-0 rounded-full bg-gradient-to-b from-primary to-secondary px-4 py-2 tracking-wide text-white md:inline-block md:px-8 md:py-3"
                >
                    {{ $mainMenu->cta_label }}
                </a>
            @endif

            @if ($mainMenu)
                <button
                        type="button"
                        @click="mobileOpen = !mobileOpen"
                        :aria-expanded="mobileOpen"
                        aria-controls="mobile-menu"
                        aria-label="Toggle menu"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 text-slate-600 hover:border-slate-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary md:hidden"
                >
                    <x-heroicon-o-bars-3 class="h-6 w-6" x-show="!mobileOpen"/>
                    <x-heroicon-o-x-mark class="h-6 w-6" x-show="mobileOpen" x-cloak/>
                </button>
            @endif
        </div>
    </div>

    @if ($mainMenu)
        <div
                id="mobile-menu"
                x-show="mobileOpen"
                x-cloak
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="border-t border-slate-200 bg-white md:hidden"
        >
            <div class="mx-auto max-w-6xl space-y-1 px-6 py-4">
                @if ($mainMenu->cta_label && $mainMenu->cta_url)
                    <a
                            href="{{ $mainMenu->cta_url }}"
                            wire:navigate
                            @click="mobileOpen = false"
                            class="mb-3 block rounded-full bg-gradient-to-b from-primary to-secondary px-4 py-3 text-center font-semibold tracking-wide text-white"
                    >
                        {{ $mainMenu->cta_label }}
                    </a>
                @endif

                @foreach ($mainMenu->items as $item)
                    @if ($item->children->isEmpty())
                        <a
                                href="{{ $item->url }}"
                                @if ($item->open_in_new_tab) target="_blank" rel="noopener" @else wire:navigate @endif
                                @click="mobileOpen = false"
                                class="block rounded-lg px-3 py-2.5 text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-primary"
                        >
                            {{ $item->label }}
                        </a>
                    @else
                        <div x-data="{ subOpen: false }">
                            <button
                                    type="button"
                                    @click="subOpen = !subOpen"
                                    :aria-expanded="subOpen"
                                    class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-primary"
                            >
                                {{ $item->label }}
                                <x-heroicon-o-chevron-down class="h-4 w-4 shrink-0 transition" x-bind:class="{ 'rotate-180': subOpen }"/>
                            </button>
                            <div x-show="subOpen" x-cloak class="ml-3 space-y-1 border-l border-slate-100 pl-3">
                                @foreach ($item->children as $child)
                                    <a
                                            href="{{ $child->url }}"
                                            @if ($child->open_in_new_tab) target="_blank" rel="noopener"
                                            @else wire:navigate @endif
                                            @click="mobileOpen = false"
                                            class="block rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-primary"
                                    >
                                        {{ $child->label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</header>

<main class="flex-1">
    @yield('content')
</main>

<footer class="border-t border-slate-100 bg-slate-50">
    <div class="mx-auto max-w-6xl px-6 py-16">
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <a href="{{ url('/') }}" wire:navigate class="inline-block">
                    <img src="{{ asset('images/logo.webp') }}" alt="{{ config('app.name') }}" class="h-10 w-auto">
                </a>
                <p class="mt-4 max-w-xs text-sm text-slate-600">
                    Local, Nepal-based trekking experts planning guided Everest region treks — from the classic Base
                    Camp trek to heli tours.
                </p>
                <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-400">
                    NTB Licensed &middot; TAAN Member &middot; Trekking Since 2015
                </p>
            </div>

            @if ($mainMenu)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Explore</p>
                    <ul class="mt-4 space-y-3 text-sm text-slate-600">
                        @foreach ($mainMenu->items as $item)
                            <li>
                                <a
                                        href="{{ $item->url }}"
                                        @if ($item->open_in_new_tab) target="_blank" rel="noopener" @else wire:navigate @endif
                                        class="hover:text-primary"
                                >
                                    {{ $item->label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Company</p>
                <ul class="mt-4 space-y-3 text-sm text-slate-600">
                    <li><a href="{{ url('about-us') }}" wire:navigate class="hover:text-primary">About Us</a></li>
                    <li><a href="{{ url('contact-us') }}" wire:navigate class="hover:text-primary">Contact Us</a></li>
                    <li><a href="{{ url('terms-and-conditions') }}" wire:navigate class="hover:text-primary">Terms and Conditions</a></li>
                </ul>
            </div>

            @if ($generalSettings->phone || $generalSettings->whatsapp_number)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Get in Touch</p>
                    <ul class="mt-4 space-y-3 text-sm">
                        @if ($generalSettings->phone)
                            <li>
                                <a
                                        href="tel:{{ preg_replace('/[^+0-9]/', '', $generalSettings->phone) }}"
                                        class="flex items-center gap-3 text-slate-600 hover:text-primary"
                                >
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-slate-200">
                                        <x-heroicon-o-phone class="h-4 w-4" />
                                    </span>
                                    {{ $generalSettings->phone }}
                                </a>
                            </li>
                        @endif
                        @if ($generalSettings->whatsapp_number)
                            <li>
                                <a
                                        href="https://wa.me/{{ $generalSettings->whatsapp_number }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="flex items-center gap-3 text-slate-600 hover:text-primary"
                                >
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-secondary text-white">
                                        <x-heroicon-o-chat-bubble-left-right class="h-4 w-4" />
                                    </span>
                                    WhatsApp Us
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>

        <div class="mt-12 border-t border-slate-200 pt-6 text-sm text-slate-500">
            &copy; {{ now()->year }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</footer>

@livewireScripts
@include('cookie-consent::index')
</body>
</html>

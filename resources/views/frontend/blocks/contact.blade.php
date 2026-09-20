@php $generalSettings = app(\App\Settings\GeneralSettings::class); @endphp
@if ($generalSettings->phone || $generalSettings->whatsapp_number)
    <section class="mx-auto max-w-4xl px-6 py-8">
        <div class="flex flex-col gap-6 rounded-2xl border border-slate-100 bg-white p-8 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Instant Help</p>
                <p class="mt-1 text-lg font-semibold text-slate-900">{{ $data['heading'] ?? 'Call or message us directly' }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                @if ($generalSettings->phone)
                    <a href="tel:{{ preg_replace('/[^+0-9]/', '', $generalSettings->phone) }}"
                       class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-primary hover:text-primary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                        @svg('heroicon-o-phone', 'h-5 w-5')
                        {{ $generalSettings->phone }}
                    </a>
                @endif
                @if ($generalSettings->whatsapp_number)
                    <a href="https://wa.me/{{ $generalSettings->whatsapp_number }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 rounded-full bg-secondary px-5 py-2.5 text-sm font-semibold text-white transition hover:opacity-90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                        @svg('heroicon-o-chat-bubble-left-right', 'h-5 w-5')
                        WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </section>
@endif

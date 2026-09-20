<div class="js-cookie-consent cookie-consent fixed bottom-0 inset-x-0 pb-4 z-50">
    <div class="mx-auto max-w-6xl px-6">
        <div class="flex flex-wrap items-center justify-between gap-4 rounded-lg border border-slate-200 bg-white p-4 shadow-lg">
            <div class="max-w-full flex-1 md:w-0">
                <p class="cookie-consent__message text-sm text-slate-600">
                    {!! trans('cookie-consent::texts.message') !!}
                </p>
            </div>
            <div class="w-full shrink-0 sm:w-auto">
                <button class="js-cookie-consent-agree cookie-consent__agree flex w-full cursor-pointer items-center justify-center rounded-full bg-primary px-6 py-2.5 text-sm font-semibold text-white transition hover:opacity-90 sm:w-auto">
                    {{ trans('cookie-consent::texts.agree') }}
                </button>
            </div>
        </div>
    </div>
</div>

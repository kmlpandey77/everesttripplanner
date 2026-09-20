@extends('frontend.layouts.app')

@php
    $fieldClasses = 'mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 transition-colors outline-none placeholder:text-slate-400 hover:border-slate-400 focus-visible:border-primary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary';
    $labelClasses = 'block text-xs font-semibold uppercase tracking-wide text-slate-400';
@endphp

@section('content')
    <div class="mx-auto max-w-3xl px-6 py-16 sm:py-20">
        <div class="text-center">
            <h1 class="text-balance text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">Make an Enquiry</h1>
            <p class="text-balance mx-auto mt-4 max-w-2xl text-lg text-slate-600">
                Have a question about a trek, or want a quick quote? Send us the details and our team will get back
                to you shortly.
            </p>
        </div>

        @if (session('status'))
            <div role="status" class="mt-10 flex items-start gap-3 rounded-lg border border-secondary/30 bg-secondary/10 px-4 py-3 text-sm text-secondary">
                <x-heroicon-o-check-circle class="mt-0.5 h-5 w-5 shrink-0" />
                <p>{{ session('status') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" class="mt-10 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <x-heroicon-o-exclamation-triangle class="mt-0.5 h-5 w-5 shrink-0" />
                <p>Please fix the {{ $errors->count() === 1 ? 'error' : $errors->count().' errors' }} below and try again.</p>
            </div>
        @endif

        <form
                method="POST"
                action="{{ route('enquiry.store') }}"
                x-data="{ submitting: false }"
                x-on:submit="submitting = true"
                class="mt-10 space-y-6"
        >
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="name" class="{{ $labelClasses }}">Name <span class="text-action">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autocomplete="name"
                           aria-required="true" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                           class="{{ $fieldClasses }} {{ $errors->has('name') ? 'border-red-300' : '' }}">
                    @error('name')
                        <p id="name-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="{{ $labelClasses }}">Email <span class="text-action">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email"
                           aria-required="true" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                           class="{{ $fieldClasses }} {{ $errors->has('email') ? 'border-red-300' : '' }}">
                    @error('email')
                        <p id="email-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="{{ $labelClasses }}">Phone</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" autocomplete="tel"
                           class="{{ $fieldClasses }} {{ $errors->has('phone') ? 'border-red-300' : '' }}">
                    @error('phone')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="trip_id" class="{{ $labelClasses }}">Trip Interested In</label>
                    <select name="trip_id" id="trip_id" class="{{ $fieldClasses }} {{ $errors->has('trip_id') ? 'border-red-300' : '' }}">
                        <option value="">Not sure yet</option>
                        @foreach ($trips as $trip)
                            <option value="{{ $trip->id }}" @selected((string) $selectedTripId === (string) $trip->id)>{{ $trip->title }}</option>
                        @endforeach
                    </select>
                    @error('trip_id')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="preferred_start_date" class="{{ $labelClasses }}">Preferred Start Date</label>
                    <input type="date" name="preferred_start_date" id="preferred_start_date" value="{{ old('preferred_start_date') }}"
                           min="{{ now()->toDateString() }}"
                           class="{{ $fieldClasses }} {{ $errors->has('preferred_start_date') ? 'border-red-300' : '' }}">
                    @error('preferred_start_date')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="group_size" class="{{ $labelClasses }}">Group Size</label>
                    <input type="number" min="1" max="100" name="group_size" id="group_size" value="{{ old('group_size') }}"
                           class="{{ $fieldClasses }} {{ $errors->has('group_size') ? 'border-red-300' : '' }}">
                    @error('group_size')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="budget_range" class="{{ $labelClasses }}">Budget Range</label>
                    <input type="text" name="budget_range" id="budget_range" value="{{ old('budget_range') }}" placeholder="e.g. $1,500 - $2,500 per person"
                           class="{{ $fieldClasses }} {{ $errors->has('budget_range') ? 'border-red-300' : '' }}">
                    @error('budget_range')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="message" class="{{ $labelClasses }}">Message</label>
                    <textarea name="message" id="message" rows="4"
                              class="{{ $fieldClasses }} {{ $errors->has('message') ? 'border-red-300' : '' }}">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <p class="text-xs text-slate-400"><span class="text-action">*</span> Required</p>

            <button
                    type="submit"
                    x-bind:disabled="submitting"
                    class="w-full rounded-full bg-secondary px-8 py-3 text-center text-sm font-semibold text-white transition hover:opacity-90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary disabled:cursor-not-allowed disabled:opacity-70 sm:w-auto"
            >
                <span x-show="!submitting">Send My Enquiry</span>
                <span x-show="submitting" x-cloak>Sending&hellip;</span>
            </button>
        </form>
    </div>
@endsection

<?php

namespace App\Http\Controllers;

use App\Enums\InquirySource;
use App\Http\Requests\StoreTripInquiryRequest;
use App\Models\Trip;
use App\Models\TripInquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanTripController extends Controller
{
    public function create(Request $request): View
    {
        $trips = Trip::query()->orderBy('title')->get()->filter->isPublished()->values();

        $title = 'Start Planning Your Trip';
        $description = 'Tell us about the Everest trek you have in mind and our Nepal-based team will follow up with a tailored itinerary and quote.';

        return view('frontend.plan-trip', [
            'trips' => $trips,
            'selectedTripId' => old('trip_id', $request->integer('trip_id') ?: null),
            'seo' => [
                'title' => "{$title} — ".config('app.name'),
                'description' => $description,
                'canonical' => url('/start-planning'),
                'robots' => 'index,follow',
                'og_title' => $title,
                'og_description' => $description,
                'og_image' => null,
                'twitter_card' => null,
                'twitter_title' => $title,
                'twitter_description' => $description,
                'twitter_image' => null,
                'schema_ld' => json_encode([
                    '@context' => 'https://schema.org',
                    '@type' => 'ContactPage',
                    'name' => $title,
                ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
            ],
        ]);
    }

    public function store(StoreTripInquiryRequest $request): RedirectResponse
    {
        TripInquiry::create([...$request->validated(), 'source' => InquirySource::PlanTrip]);

        return back()->with('status', 'Thanks! We\'ve received your trip planning request and will be in touch shortly.');
    }
}

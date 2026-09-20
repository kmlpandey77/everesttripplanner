<?php

namespace App\Http\Controllers;

use App\Enums\InquirySource;
use App\Http\Requests\StoreTripInquiryRequest;
use App\Models\Trip;
use App\Models\TripInquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnquiryController extends Controller
{
    public function create(Request $request): View
    {
        $trips = Trip::query()->orderBy('title')->get()->filter->isPublished()->values();

        $title = 'Make an Enquiry';
        $description = 'Have a question about a trek, or want a quick quote? Send us the details and our Nepal-based team will get back to you.';

        return view('frontend.make-enquiry', [
            'trips' => $trips,
            'selectedTripId' => old('trip_id', $request->integer('trip_id') ?: null),
            'seo' => [
                'title' => "{$title} — ".config('app.name'),
                'description' => $description,
                'canonical' => url('/make-enquiry'),
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
        TripInquiry::create([...$request->validated(), 'source' => InquirySource::Enquiry]);

        return back()->with('status', 'Thanks! We\'ve received your enquiry and will be in touch shortly.');
    }
}

<?php

namespace App\Models;

use App\Enums\InquirySource;
use App\Enums\TravelStyle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripInquiry extends Model
{
    protected $fillable = [
        'source',
        'name',
        'email',
        'phone',
        'travel_style',
        'trip_id',
        'preferred_start_date',
        'group_size',
        'budget_range',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'source' => InquirySource::class,
            'travel_style' => TravelStyle::class,
            'preferred_start_date' => 'date',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * @param  Builder<TripInquiry>  $query
     * @return Builder<TripInquiry>
     */
    public function scopeEnquiries(Builder $query): Builder
    {
        return $query->where('source', InquirySource::Enquiry);
    }

    /**
     * @param  Builder<TripInquiry>  $query
     * @return Builder<TripInquiry>
     */
    public function scopePlanTripRequests(Builder $query): Builder
    {
        return $query->where('source', InquirySource::PlanTrip);
    }
}

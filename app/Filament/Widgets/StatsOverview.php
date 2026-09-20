<?php

namespace App\Filament\Widgets;

use App\Enums\PageStatus;
use App\Models\Review;
use App\Models\Trip;
use App\Models\TripInquiry;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Inquiries This Month', TripInquiry::query()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count())
                ->description('Trip planner & enquiry submissions')
                ->color('primary'),

            Stat::make('Published Trips', Trip::query()->where('status', PageStatus::Published)->count())
                ->description(Trip::query()->count().' total')
                ->color('success'),

            Stat::make('Reviews Awaiting Approval', Review::query()->where('is_published', false)->count())
                ->description('Unpublished reviews')
                ->color('warning'),
        ];
    }
}

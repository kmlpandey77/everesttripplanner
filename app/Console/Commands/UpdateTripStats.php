<?php

namespace App\Console\Commands;

use App\Models\Trip;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:update-trip-stats')]
#[Description('Refresh the "currently viewing" and "last booked" social-proof stats on every trip')]
class UpdateTripStats extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Trip::query()
            ->select(['id', 'last_booked_at'])
            ->chunkById(200, function ($trips): void {
                foreach ($trips as $trip) {
                    $now = now();

                    if (!$trip->last_booked_at) {
                        $lastBookedAt = $now->copy()
                            ->subDays(random_int(1, 10))
                            ->addHours(random_int(0, 23));
                    } else {
                        $lastBookedAt = $trip->last_booked_at
                            ->copy()
                            ->addHours(random_int(0, 23));
                    }

                    if ($lastBookedAt->greaterThan($now)) {
                        $lastBookedAt = $now;
                    }

                    $trip->update([
                        'last_booked_at' => $lastBookedAt,
                    ]);
                }
            });

        $this->info('Trip stats updated.');
    }
}

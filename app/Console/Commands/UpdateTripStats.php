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
        Trip::query()->select('id')->chunkById(200, function ($trips): void {
            foreach ($trips as $trip) {
                $trip->update([
                    //                    'currently_viewing' => random_int(20, 80),
                    'last_booked_at' => now()->subDays(random_int(1, 10)),
                ]);
            }
        });

        $this->info('Trip stats updated.');
    }
}

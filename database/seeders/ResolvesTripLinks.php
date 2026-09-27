<?php

namespace Database\Seeders;

use App\Models\Trip;

/**
 * Trip URLs differ per environment (nested under the category locally, flat in production),
 * so blog content links to trips as href="trip:{slug}" and this swaps in the trip's real route path.
 */
trait ResolvesTripLinks
{
    protected function resolveTripLinks(string $html): string
    {
        return preg_replace_callback('/href="trip:([a-z0-9-]+)"/', function (array $match): string {
            $trip = Trip::query()->with('route')->where('slug', $match[1])->firstOrFail();

            return 'href="/'.$trip->route->full_path.'"';
        }, $html);
    }
}

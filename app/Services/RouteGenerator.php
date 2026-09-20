<?php

namespace App\Services;

use App\Contracts\Routable;
use App\Exceptions\InvalidRouteException;
use App\Models\Route as RouteModel;
use Illuminate\Support\Str;

class RouteGenerator
{
    private const RESERVED_SLUGS = ['admin', 'storage', 'api', 'livewire', 'build', 'assets', 'start-planning', 'make-enquiry'];

    /**
     * Create or update a Route owned by the given routable model.
     *
     * A routable is matched by (routable_type, routable_id, type), not just
     * (routable_type, routable_id) — this lets a single model own more than
     * one route "slot" (e.g. a Trip's main page and its detailed-itinerary
     * page), each addressed by its own $type. $relation optionally names the
     * Eloquent relation on $routable to refresh, for callers that read
     * $routable->{$relation} immediately after sync().
     */
    public function sync(Routable $routable, string $slug, string $type, ?RouteModel $parent = null, string $relation = 'route'): RouteModel
    {
        $slug = Str::slug($slug);
        $fullPath = $parent ? trim($parent->full_path, '/').'/'.$slug : $slug;

        $existing = RouteModel::query()
            ->where('routable_type', $routable->getMorphClass())
            ->where('routable_id', $routable->getKey())
            ->where('type', $type)
            ->first();

        $this->releaseStaleRedirect($fullPath);
        $this->guardReserved($fullPath);
        $this->guardUnique($fullPath, $existing);

        $route = RouteModel::updateOrCreate(
            [
                'routable_type' => $routable->getMorphClass(),
                'routable_id' => $routable->getKey(),
                'type' => $type,
            ],
            [
                'slug' => $slug,
                'full_path' => $fullPath,
                'parent_id' => $parent?->id,
                'is_active' => true,
            ],
        );

        if ($existing && $existing->full_path !== $fullPath) {
            $this->leaveRedirect($existing, $fullPath);
        }

        if (method_exists($routable, $relation)) {
            $routable->setRelation($relation, $route);
        }

        return $route;
    }

    /**
     * Free up $fullPath if it's currently only held by a stale auto-redirect
     * stub, so a slug can reclaim a path it previously redirected away from.
     */
    private function releaseStaleRedirect(string $fullPath): void
    {
        RouteModel::query()
            ->where('full_path', $fullPath)
            ->whereNull('routable_id')
            ->where('type', 'redirect')
            ->delete();
    }

    /**
     * Leave a redirect stub at the routable's old path, and retarget any
     * redirects that pointed there so slug changes never chain more than
     * one hop.
     */
    private function leaveRedirect(RouteModel $existing, string $newFullPath): void
    {
        RouteModel::query()
            ->where('redirect_to', $existing->full_path)
            ->update(['redirect_to' => $newFullPath]);

        RouteModel::updateOrCreate(
            ['full_path' => $existing->full_path],
            [
                'slug' => $existing->slug,
                'type' => 'redirect',
                'routable_type' => null,
                'routable_id' => null,
                'parent_id' => null,
                'redirect_to' => $newFullPath,
                'redirect_code' => 301,
                'is_active' => true,
            ],
        );
    }

    public static function isReserved(string $slug): bool
    {
        return in_array(Str::slug($slug), self::RESERVED_SLUGS, true);
    }

    private function guardReserved(string $fullPath): void
    {
        $root = Str::before($fullPath, '/');

        if (self::isReserved($root)) {
            throw new InvalidRouteException("The path segment \"{$root}\" is reserved and cannot be used.");
        }
    }

    private function guardUnique(string $fullPath, ?RouteModel $existing): void
    {
        $conflict = RouteModel::query()
            ->where('full_path', $fullPath)
            ->when($existing, fn ($query) => $query->whereKeyNot($existing->id))
            ->exists();

        if ($conflict) {
            throw new InvalidRouteException("The path \"/{$fullPath}\" is already in use.");
        }
    }
}

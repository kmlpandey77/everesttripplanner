<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Page;
use App\Models\Route as RouteModel;
use App\Models\Trip;
use App\Services\RouteResolver;
use App\Services\SeoService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontendController extends Controller
{
    public function __construct(private readonly SeoService $seo) {}

    public function __invoke(Request $request, RouteResolver $resolver, string $path = ''): View|RedirectResponse
    {
        $route = $resolver->resolve($path);

        abort_unless($route, 404);

        if ($route->redirect_to) {
            $target = preg_replace('/\s+/', '', $route->redirect_to);

            if (! str_contains($target, '://')) {
                $target = '/'.ltrim($target, '/');
            }

            return redirect($target, $route->redirect_code ?? 302);
        }

        return match ($route->type) {
            'page' => $this->renderPage($route, $request),
            'category' => $this->renderCategory($route, $request),
            'trip' => $this->renderTrip($route, $request),
            'trip-itinerary' => $this->renderTripItinerary($route, $request),
            'blog' => $this->renderBlog($route, $request),
            default => abort(404),
        };
    }

    private function renderPage(RouteModel $route, Request $request): View
    {
        $page = $route->routable;

        abort_unless($page instanceof Page && $page->isPublished(), 404);

        return view('frontend.page', [
            'page' => $page,
            'siblingPages' => $this->siblingPages($route),
            'seo' => $this->seo->resolve($page, $request->url()),
        ]);
    }

    private function renderBlog(RouteModel $route, Request $request): View
    {
        $blog = $route->routable;

        abort_unless($blog instanceof Blog && $blog->isPublished(), 404);

        return view('frontend.blog-post', [
            'blog' => $blog,
            'seo' => $this->seo->resolve($blog, $request->url()),
        ]);
    }

    /**
     * @return Collection<int, RouteModel>
     */
    private function siblingPages(RouteModel $route): Collection
    {
        if ($route->parent_id === null) {
            return new Collection;
        }

        return RouteModel::query()
            ->where('parent_id', $route->parent_id)
            ->where('type', 'page')
            ->where('is_active', true)
            ->with('routable')
            ->get()
            ->filter(fn (RouteModel $sibling): bool => $sibling->routable instanceof Page && $sibling->routable->isPublished())
            ->values();
    }

    private function renderCategory(RouteModel $route, Request $request): View
    {
        $category = $route->routable;

        abort_unless($category instanceof Category, 404);

        return view('frontend.category', [
            'category' => $category->load(['children.route', 'trips.route', 'trips.publishedReviews']),
            'seo' => $this->seo->resolve($category, $request->url()),
        ]);
    }

    private function renderTrip(RouteModel $route, Request $request): View
    {
        $trip = $route->routable;

        abort_unless($trip instanceof Trip && $trip->isPublished(), 404);

        return view('frontend.trip', [
            'trip' => $trip,
            'seo' => $this->seo->resolve($trip, $request->url()),
        ]);
    }

    private function renderTripItinerary(RouteModel $route, Request $request): View
    {
        $trip = $route->routable;

        abort_unless($trip instanceof Trip && $trip->isPublished() && filled($trip->detailed_itinerary), 404);

        $title = $trip->title.' — Detailed Itinerary';
        $image = $trip->galleryMedia()->first()?->url;

        return view('frontend.trip-itinerary', [
            'trip' => $trip,
            'seo' => [
                'title' => $title,
                'description' => $trip->defaultMetaDescription(),
                'canonical' => $request->url(),
                'robots' => 'index,follow',
                'og_title' => $title,
                'og_description' => $trip->defaultMetaDescription(),
                'og_image' => $image,
                'twitter_card' => $image ? 'summary_large_image' : null,
                'twitter_title' => $title,
                'twitter_description' => $trip->defaultMetaDescription(),
                'twitter_image' => $image,
                'schema_ld' => $this->seo->graph([[
                    '@type' => 'WebPage',
                    'name' => $title,
                    'url' => $request->url(),
                    'description' => $trip->defaultMetaDescription(),
                    'about' => ['@type' => 'TouristTrip', 'name' => $trip->title, 'url' => url($trip->route->full_path)],
                ]]),
            ],
        ]);
    }
}

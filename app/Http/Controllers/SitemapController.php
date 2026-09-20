<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Page;
use App\Models\Trip;
use Illuminate\Http\Response;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $sitemap = Sitemap::create();

        Trip::with(['route', 'itineraryRoute'])->get()->filter->isPublished()->each(function (Trip $trip) use ($sitemap): void {
            if ($trip->route) {
                $sitemap->add(Url::create($trip->route->full_path)->setLastModificationDate($trip->updated_at));
            }

            if ($trip->itineraryRoute) {
                $sitemap->add(Url::create($trip->itineraryRoute->full_path)->setLastModificationDate($trip->updated_at));
            }
        });

        Page::with('route')->get()->filter->isPublished()->each(function (Page $page) use ($sitemap): void {
            if ($page->route) {
                $sitemap->add(Url::create($page->route->full_path ?: '/')->setLastModificationDate($page->updated_at));
            }
        });

        Category::with('route')->get()->each(function (Category $category) use ($sitemap): void {
            if ($category->route) {
                $sitemap->add(Url::create($category->route->full_path)->setLastModificationDate($category->updated_at));
            }
        });

        Blog::with('route')->get()->filter->isPublished()->each(function (Blog $blog) use ($sitemap): void {
            if ($blog->route) {
                $sitemap->add(Url::create($blog->route->full_path)->setLastModificationDate($blog->updated_at));
            }
        });

        return response($sitemap->render(), 200, ['Content-Type' => 'application/xml']);
    }
}

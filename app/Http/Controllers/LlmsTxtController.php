<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Page;
use App\Models\Trip;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class LlmsTxtController extends Controller
{
    /**
     * Publish an llms.txt content map so AI assistants and answer engines
     * can discover and cite published trips, categories, and pages.
     *
     * @see https://llmstxt.org/
     */
    public function __invoke(): Response
    {
        $trips = Trip::with('route')->get()->filter->isPublished();
        $categories = Category::with('route')->orderBy('name')->get();
        $pages = Page::with('route')->get()->filter->isPublished();
        $blogs = Blog::with('route')->get()->filter->isPublished();

        $lines = [
            '# '.config('app.name'),
            '',
            '> Trip planning content for the Everest region and beyond — treks, categories, informational pages, and blog posts.',
        ];

        $lines = array_merge($lines, $this->section('Trips', $trips, fn (Trip $trip): string => $trip->defaultMetaDescription() ?? ''));
        $lines = array_merge($lines, $this->section('Categories', $categories));
        $lines = array_merge($lines, $this->section('Pages', $pages));
        $lines = array_merge($lines, $this->section('Blogs', $blogs, fn (Blog $blog): string => $blog->defaultMetaDescription() ?? ''));

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /**
     * @param  Collection<int, Page|Category|Trip|Blog>  $items
     * @return array<int, string>
     */
    private function section(string $title, $items, ?callable $describe = null): array
    {
        $items = $items->filter(fn ($item) => $item->route !== null)->values();

        if ($items->isEmpty()) {
            return [];
        }

        $lines = ['', "## {$title}", ''];

        foreach ($items as $item) {
            $label = $item->title ?? $item->name;
            $url = url($item->route->full_path);
            $description = $describe ? $describe($item) : '';

            $lines[] = $description !== ''
                ? "- [{$label}]({$url}): {$description}"
                : "- [{$label}]({$url})";
        }

        return $lines;
    }
}

<?php

namespace App\Services;

use App\Models\Route;

class RouteResolver
{
    public function resolve(string $path): ?Route
    {
        return Route::query()
            ->where('full_path', trim($path, '/'))
            ->where('is_active', true)
            ->with('routable')
            ->first();
    }
}

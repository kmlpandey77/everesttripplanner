<?php

namespace App\Contracts;

use App\Models\Route;
use Illuminate\Database\Eloquent\Relations\MorphOne;

interface Routable
{
    /**
     * @return MorphOne<Route, $this>
     */
    public function route(): MorphOne;
}

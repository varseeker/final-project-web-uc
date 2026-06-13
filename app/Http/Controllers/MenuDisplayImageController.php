<?php

namespace App\Http\Controllers;

use App\Support\MenuImageOptimizer;
use Illuminate\Http\Request;

class MenuDisplayImageController extends Controller
{
    public function show(Request $request, string $v, string $src)
    {
        $source = MenuImageOptimizer::decodeSource($src);

        return MenuImageOptimizer::render($source, $v);
    }
}

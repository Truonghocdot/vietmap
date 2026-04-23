<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Support\MirrorPageRenderer;
use Illuminate\Http\Response;

class StorefrontController extends Controller
{
    public function __invoke(MirrorPageRenderer $renderer): Response
    {
        $html = $renderer->renderHome(
            Package::query()->active()->get()
        );

        return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
    }
}

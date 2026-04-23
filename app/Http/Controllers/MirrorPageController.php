<?php

namespace App\Http\Controllers;

use App\Support\MirrorPageRenderer;
use Illuminate\Http\Response;

class MirrorPageController extends Controller
{
    public function page(string $page, MirrorPageRenderer $renderer): Response
    {
        $relativePath = str_ends_with($page, '.html') ? $page : "{$page}.html";

        return response($renderer->render($relativePath))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function blog(string $slug, MirrorPageRenderer $renderer): Response
    {
        return response($renderer->render("blog/{$slug}.html"))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }
}

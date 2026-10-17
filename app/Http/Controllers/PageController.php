<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use League\CommonMark\CommonMarkConverter;

class PageController extends Controller
{
    public function show(Request $request, string $page)
    {
        $path = resource_path("pages/{$page}.md");

        if (! File::exists($path)) {
            abort(404);
        }

        $markdown = File::get($path);

        $replacements = [
            '{{SITE_NAME}}' => config('site.name'),
            '{{SITE_URL}}' => config('app.url'),
            '{{CONTACT_EMAIL}}' => config('site.contact_email'),
        ];

        $markdown = str_replace(array_keys($replacements), array_values($replacements), $markdown);

        $converter = new CommonMarkConverter();
        $html = $converter->convert($markdown)->getContent();

        return view('page', [
            'title' => config('site.name'),
            'content' => $html,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Placeholder page for admin modules that are planned but not built yet
 * (route: admin.coming-soon, e.g. /admin/coming-soon/survey-kepuasan).
 */
class ComingSoonController extends Controller
{
    public function show(string $module): View
    {
        $title = Str::of($module)->replace(['-', '_'], ' ')->title();

        return view('admin.coming-soon', ['title' => $title]);
    }
}

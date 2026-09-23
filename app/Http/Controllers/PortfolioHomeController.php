<?php

namespace App\Http\Controllers;

use App\Support\FactHtmlSanitizer;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioHomeController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Welcome', [
            'projects' => DB::table('projects')->orderByDesc('created_at')->orderByDesc('id')->get(),
            'skills' => DB::table('skills')->orderBy('id')->get(),
            'facts' => DB::table('facts')->orderBy('id')->get()->map(function (object $fact) {
                $fact->text = FactHtmlSanitizer::clean($fact->text);
                return $fact;
            }),
        ]);
    }
}

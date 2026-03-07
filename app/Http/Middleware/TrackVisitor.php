<?php

namespace App\Http\Middleware;

use App\Models\Visitor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    public function handle(Request $request, Closure $next): Response
    {
        // Skip bots and crawlers
        $userAgent = $request->userAgent();
        if ($this->isBot($userAgent)) {
            return $next($request);
        }

        // Skip admin routes
        if ($request->is('admin/*')) {
            return $next($request);
        }

        // Skip API routes
        if ($request->is('api/*')) {
            return $next($request);
        }

        // Record visitor
        Visitor::recordVisit($request);

        return $next($request);
    }

    protected function isBot($userAgent): bool
    {
        $bots = [
            'googlebot', 'bingbot', 'slurp', 'duckduckbot',
            'baiduspider', 'yandexbot', 'sogou', 'exabot',
            'facebot', 'ia_archiver', 'spider', 'crawler', 'bot'
        ];

        foreach ($bots as $bot) {
            if (stripos($userAgent, $bot) !== false) {
                return true;
            }
        }

        return false;
    }
}

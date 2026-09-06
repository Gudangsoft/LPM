<?php

namespace App\Http\Middleware;

use App\Models\DkpsSubmission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDkpsOwnership
{
    /**
     * Blocks a kaprodi from reaching any dkps/{dkp}/... route (the submission
     * itself, its export, or any of the ~30 nested section actions across the
     * DKPS sub-controllers) unless the resolved DkpsSubmission belongs to a
     * prodi they head. No-ops for index/create/store (no {dkp} bound yet -
     * those are scoped separately in DkpsController) and for every other role.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !$user->isAdmin() && $user->isKaprodi()) {
            $dkp = $request->route('dkp');

            if ($dkp instanceof DkpsSubmission && $dkp->prodi?->kaprodi_id !== $user->id) {
                abort(403, 'Anda hanya dapat mengakses data DKPS program studi Anda sendiri.');
            }
        }

        return $next($request);
    }
}

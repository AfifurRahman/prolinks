<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Project;
use Auth;

class VerifyProjectMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->type == 0) {
            $models = Project::where('client_id', \globals::get_client_id())->get();
            if ($models->count() == 0) {
            // if (false) {
                /* jika admin client belum memiliki project sama sekali */
                return redirect(route('create-new-project'));
            }
        }

        return $next($request);
    }
}

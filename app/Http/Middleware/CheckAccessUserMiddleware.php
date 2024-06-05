<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Project;
use Auth;
use App\Models\AssignProject;

class CheckAccessUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /* check if user disabled */
        $check = AssignProject::where('user_id', Auth::user()->user_id)->where('client_id', Auth::user()->client_id)->where('deleted', 1)->get();
        if (count($check) > 0) {
            abort('404');
        }

        return $next($request);
    }
}

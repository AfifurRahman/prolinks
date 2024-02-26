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
        if (Auth::user()->type == \globals::set_usertype_admin()) {
            $models = Project::where('user_id', Auth::user()->user_id)->get();
            if ($models->count() == 0) {
                /* jika admin client belum memiliki project sama sekali */
                return redirect(route('create-new-project'));
            }
        }

        return $next($request);
    }
}

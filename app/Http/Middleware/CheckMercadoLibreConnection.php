<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use WebDEV\Meli\Services\MeliApiService;

class CheckMercadoLibreConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $service = new MeliApiService(auth()->user()->id);
            $service->validateToken();
            return $next($request);
        } catch(\Exception $e) {
            $redirectUri = route('meli.token');
            $link = $service->getAuthUrl($redirectUri);
            return redirect()->away($link);
        }
    }
}

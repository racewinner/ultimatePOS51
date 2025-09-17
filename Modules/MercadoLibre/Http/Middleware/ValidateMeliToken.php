<?php

namespace App\Modules\MercadoLibre\Http\Middleware;

use Closure;
use WebDEV\Meli\Services\MeliApiService;

class ValidateMeliToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $service = new MeliApiService('testuser');
            $service->validateToken();

            return $next($request);
        } catch(Exception $e) {
            dd($e);
        }
    }
}

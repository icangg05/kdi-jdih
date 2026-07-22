<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Ambil locale dari prefix URL frontend ({locale}) bila ada, kalau tidak
     * pakai session / default. Tidak me-redirect path non-frontend (backend,
     * auth, livewire, dll) — locale cukup di-set dari session untuk mereka.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('app.supported_locales', ['id']);
        $seg = $request->segment(1);

        $locale = in_array($seg, $supported, true)
            ? $seg
            : session('locale', config('app.locale'));

        app()->setLocale($locale);
        session(['locale' => $locale]);

        // Supaya route('frontend.*') otomatis mengisi prefix {locale}
        URL::defaults(['locale' => $locale]);
        if ($request->route()) {
            $request->route()->forgetParameter('locale');
        }

        return $next($request);
    }
}

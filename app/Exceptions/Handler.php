<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Convert an authentication exception into a response.
     *
     * The app polls several JSON endpoints in the background (notification
     * bell, chat, availability calendars, etc.) via plain fetch() calls that
     * don't always send an "Accept: application/json" header. Laravel's
     * default expectsJson() check can miss those, which means an expired
     * session during a background poll gets treated as "a guest trying to
     * visit a page" — and that URL then gets remembered and used as the
     * post-login redirect target, sending people to a raw JSON endpoint
     * instead of their dashboard. We widen the AJAX detection here so that
     * only genuine full-page navigations ever get remembered.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($this->isAjaxLikeRequest($request)) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }

        return redirect()->guest($exception->redirectTo() ?? route('login'));
    }

    private function isAjaxLikeRequest($request): bool
    {
        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return true;
        }

        // Modern browsers tag real address-bar navigations / link clicks as
        // Sec-Fetch-Mode: navigate. Background fetch() calls are tagged
        // "cors" or "same-origin" instead.
        $fetchMode = $request->header('Sec-Fetch-Mode');
        if ($fetchMode && $fetchMode !== 'navigate') {
            return true;
        }

        return false;
    }
}
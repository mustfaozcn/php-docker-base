<?php

namespace App\Middleware;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthMiddleware
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function handle(Request $request, callable $next)
    {
        $publicRoutes = ['/login', '/ajax-login']; // Herkese açık rotaları burada belirtin

        if (!in_array($request->getPathInfo(), $publicRoutes) && !$this->session->has('user')) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['error' => 'Unauthorized', 'redirect' => '/login'], 401);
            } else {
                return new RedirectResponse('/login');
            }
        }

        return $next($request);
    }
}
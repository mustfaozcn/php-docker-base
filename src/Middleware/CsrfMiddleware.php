<?php

namespace App\Middleware;

use App\CsrfProtection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CsrfMiddleware
{
    private $csrfProtection;

    public function __construct(CsrfProtection $csrfProtection)
    {
        $this->csrfProtection = $csrfProtection;
    }

    public function handle(Request $request, callable $next)
    {
        // CSRF koruması sadece POST, PUT, DELETE ve PATCH istekleri için gerekli
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $token = $request->headers->get('X-CSRF-TOKEN');

            if (!$token || !$this->csrfProtection->validateToken($token)) {
                return new JsonResponse(['error' => 'CSRF token validation failed'], 403);
            }
        }

        $response = $next($request);

        // Eğer yanıt zaten bir Response nesnesi ise, onu olduğu gibi döndür
        if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
            return $response;
        }

        // Eğer yanıt bir dizi ise, onu JsonResponse'a çevir
        if (is_array($response)) {
            return new JsonResponse($response);
        }

        // Diğer durumlar için, yanıtı olduğu gibi döndür
        return $response;
    }
}
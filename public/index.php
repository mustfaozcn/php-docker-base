<?php
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/bootstrap.php';

use FastRoute\RouteCollector;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\LdapAuth;
use App\CsrfProtection;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

// Statik dosyaları işle
$requestUri = $_SERVER['REQUEST_URI'];
if (preg_match('/\.(?:css|js|png|jpg|gif)$/', $requestUri)) {
    $filePath = __DIR__ . $requestUri;
    if (file_exists($filePath)) {
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
        ];
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        header('Content-Type: ' . $mimeTypes[$ext]);
        readfile($filePath);
        exit;
    }
}

try {
    $entityManager = require __DIR__ . '/../src/bootstrap.php';
    $config = require __DIR__ . '/../src/config.php';

    $ldapAuth = new LdapAuth($config['ldap']);
    $session = new Session();
    $session->start();

    $csrfProtection = new CsrfProtection($session);
    $authMiddleware = new AuthMiddleware($session);
    $csrfMiddleware = new CsrfMiddleware($csrfProtection);

    $dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
        $r->addRoute('GET', '/', [HomeController::class, 'index']);
        $r->addRoute('GET', '/login', [AuthController::class, 'loginForm']);
        $r->addRoute('POST', '/login', [AuthController::class, 'login']);
        $r->addRoute('POST', '/ajax-login', [AuthController::class, 'ajaxLogin']);
        $r->addRoute('GET', '/logout', [AuthController::class, 'logout']);

        // User CRUD routes
        $r->addRoute('GET', '/users', [UserController::class, 'index']);
        $r->addRoute('GET', '/users/list', [UserController::class, 'list']);
        $r->addRoute('GET', '/users/{id:\d+}', [UserController::class, 'show']);
        $r->addRoute('POST', '/users/create', [UserController::class, 'create']);
        $r->addRoute('PUT', '/users/{id:\d+}', [UserController::class, 'edit']);
        $r->addRoute('DELETE', '/users/{id:\d+}', [UserController::class, 'delete']);

    });

    $request = Request::createFromGlobals();

    // AuthMiddleware ve CsrfMiddleware'i uygula
    $response = $authMiddleware->handle($request, function ($request) use ($csrfMiddleware, $dispatcher, $entityManager, $ldapAuth, $session, $csrfProtection) {
        return $csrfMiddleware->handle($request, function ($request) use ($dispatcher, $entityManager, $ldapAuth, $session, $csrfProtection) {
            $uri = $request->getPathInfo();
            $method = $request->getMethod();

            $routeInfo = $dispatcher->dispatch($method, $uri);

            switch ($routeInfo[0]) {
                case FastRoute\Dispatcher::NOT_FOUND:
                    return new JsonResponse(['error' => 'Not Found'], 404);
                case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                    return new JsonResponse(['error' => 'Method Not Allowed'], 405);
                case FastRoute\Dispatcher::FOUND:
                    $handler = $routeInfo[1];
                    $vars = $routeInfo[2];

                    $controllerName = $handler[0];
                    $methodName = $handler[1];

                    $controller = new $controllerName($entityManager, $ldapAuth, $session, $csrfProtection);
                    return $controller->$methodName($request, $vars);
            }
        });
    });

    // Yanıtı gönder
    if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
        $response->send();
    } elseif (is_array($response)) {
        $jsonResponse = new JsonResponse($response);
        $jsonResponse->send();
    } else {
        echo $response;
    }

} catch (\Exception $e) {
    // Hata mesajını loglayın
    error_log($e->getMessage());

    // Kullanıcıya genel bir hata mesajı gönderin
    $errorResponse = new JsonResponse(['error' => 'An error occurred. Please try again later.'], 500);
    $errorResponse->send();
}
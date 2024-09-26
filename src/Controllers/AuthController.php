<?php

namespace App\Controllers;

use App\LdapAuth;
use App\CsrfProtection;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController
{
    private $entityManager;
    private $ldapAuth;
    private $session;
    private $csrfProtection;

    public function __construct($entityManager, LdapAuth $ldapAuth, SessionInterface $session, CsrfProtection $csrfProtection)
    {
        $this->entityManager = $entityManager;
        $this->ldapAuth = $ldapAuth;
        $this->session = $session;
        $this->csrfProtection = $csrfProtection;
    }

    public function loginForm()
    {
        $csrfToken = $this->csrfProtection->generateToken();
        include __DIR__ . '/../../templates/basic_crud_template/login.php';
    }

    public function login()
    {
        if (!$this->csrfProtection->validateToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token validation failed');
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($this->ldapAuth->authenticate($username, $password)) {
            $this->session->set('user', $username);
            header('Location: /users'); // CRUD sayfasına yönlendir
            exit;
        } else {
            $error = 'Geçersiz kullanıcı adı veya şifre';
            include __DIR__ . '/../../templates/basic_crud_template/login.php';
        }
    }

    public function ajaxLogin(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(['error' => 'This endpoint only accepts AJAX requests'], 400);
        }

        $csrfToken = $request->headers->get('X-CSRF-TOKEN');
        if (!$this->csrfProtection->validateToken($csrfToken)) {
            return new JsonResponse(['success' => false, 'message' => 'CSRF token validation failed'], 403);
        }

        $username = $request->request->get('username');
        $password = $request->request->get('password');

        if (empty($username) || empty($password)) {
            return new JsonResponse(['success' => false, 'message' => 'Username and password are required'], 400);
        }

        if ($this->ldapAuth->authenticate($username, $password)) {
            $this->session->set('user', $username);
            return new JsonResponse(['success' => true, 'redirect' => '/users']);
        } else {
            return new JsonResponse(['success' => false, 'message' => 'Invalid username or password'], 401);
        }
    }

    public function logout()
    {
        $this->session->remove('user');
        header('Location: /login');
        exit;
    }
}
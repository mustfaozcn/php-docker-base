<?php

namespace App\Controllers;

use App\Models\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\LdapAuth;
use App\CsrfProtection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController
{
    private $entityManager;
    private $session;
    private $ldapAuth;
    private $csrfProtection;

    public function __construct($entityManager, LdapAuth $ldapAuth, SessionInterface $session, CsrfProtection $csrfProtection)
    {
        $this->entityManager = $entityManager;
        $this->ldapAuth = $ldapAuth;
        $this->session = $session;
        $this->csrfProtection = $csrfProtection;
    }

    public function index()
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $templatePath = dirname(__DIR__, 2) . '/templates/basic_crud_template/index.php';

        if (!file_exists($templatePath)) {
            return new Response('Template file not found', 500);
        }

        $csrfToken = $this->csrfProtection->generateToken();

        ob_start();
        require $templatePath;
        $content = ob_get_clean();

        return new Response($content);
    }

    public function list()
    {
        error_log('UserController::list method called');
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $usersArray = array_map(function ($user) {
            return [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail()
            ];
        }, $users);
        return new JsonResponse($usersArray, 200, ['Content-Type' => 'application/json']);
    }

    public function show($vars)
    {
        $id = $vars['id'];
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail()
        ]);
    }

    public function create(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $username = $data['username'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($username) || empty($email) || empty($password)) {
            return new JsonResponse(['error' => 'Username, email and password are required'], 400);
        }

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));

        $this->entityManager->persist($user);

        try {
            $this->entityManager->flush();
            return new JsonResponse(['message' => 'User created successfully']);
        } catch (\Exception $e) {
            error_log('Error creating user: ' . $e->getMessage());
            return new JsonResponse(['error' => 'An error occurred while creating the user'], 500);
        }
    }

    public function edit(Request $request, $vars)
    {
        $id = $vars['id'];
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $username = $data['username'] ?? '';
        $email = $data['email'] ?? '';

        if (empty($username) || empty($email)) {
            return new JsonResponse(['error' => 'Username and email are required'], 400);
        }

        $user->setUsername($username);
        $user->setEmail($email);

        try {
            $this->entityManager->flush();
            return new JsonResponse(['message' => 'User updated successfully']);
        } catch (\Exception $e) {
            error_log('Error updating user: ' . $e->getMessage());
            return new JsonResponse(['error' => 'An error occurred while updating the user'], 500);
        }
    }

    public function delete(Request $request, array $vars)
    {
        $id = $vars['id'];
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'User deleted successfully']);
    }
}
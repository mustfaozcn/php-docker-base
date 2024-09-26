<?php

namespace App\Controllers;

use App\LdapAuth;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HomeController
{
    private $entityManager;
    private $ldapAuth;
    private $session;

    public function __construct($entityManager, LdapAuth $ldapAuth, SessionInterface $session)
    {
        $this->entityManager = $entityManager;
        $this->ldapAuth = $ldapAuth;
        $this->session = $session;
    }

    public function index()
    {
        if (!$this->session->get('user')) {
            header('Location: /login');
            exit;
        }

        $users = $this->entityManager->getRepository('App\Models\User')->findAll();

        include __DIR__ . '/../Views/home.php';
    }
}
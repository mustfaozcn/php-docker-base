<?php

namespace App;

class CsrfProtection
{
    private $session;

    public function __construct($session)
    {
        $this->session = $session;
    }

    public function generateToken()
    {
        $token = bin2hex(random_bytes(32));
        $this->session->set('csrf_token', $token);
        return $token;
    }

    public function validateToken($token)
    {
        $storedToken = $this->session->get('csrf_token');
        if (!$storedToken || !hash_equals($storedToken, $token)) {
            return false;
        }
        return true;
    }
}
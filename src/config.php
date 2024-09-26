<?php
return [
    'db' => [
        'driver' => 'pdo_pgsql',
        'host' => 'DB_HOST',
        'port' => 'DB_PORT',
        'dbname' => 'DB_NAME',
        'user' => 'DB_USER',
        'password' => 'DB_PASSWORD',
    ],
    'ldap' => [
        'host' => 'LDAP_HOST',
        'port' => 389,
        'username' => 'LDAP_USER',
        'password' => 'LDAP_PASSWORD',
        'base_dn' => 'LDAP_BASE_DN',
        'timeout' => 5,
        'ssl' => false,
        'tls' => false,
    ],
];

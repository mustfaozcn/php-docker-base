<?php

namespace App;

class LdapAuth
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function authenticate($username, $password)
    {
        $ldapServer = $this->config['host'];
        $ldapPort = $this->config['port'];
        $baseDn = $this->config['base_dn'];
        $adminUsername = $this->config['username'];
        $adminPassword = $this->config['password'];

        $ldapConn = ldap_connect($ldapServer, $ldapPort);

        if ($ldapConn) {
            ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($ldapConn, LDAP_OPT_NETWORK_TIMEOUT, $this->config['timeout']);

            if ($this->config['ssl']) {
                ldap_start_tls($ldapConn);
            }

            // Önce admin kullanıcısı ile bağlan
            $adminBind = @ldap_bind($ldapConn, $adminUsername, $adminPassword);

            if ($adminBind) {
                // Kullanıcıyı ara
                $searchFilter = "(sAMAccountName=$username)";
                $search = ldap_search($ldapConn, $baseDn, $searchFilter);

                if ($search) {
                    $entries = ldap_get_entries($ldapConn, $search);
                    if ($entries['count'] > 0) {
                        $userDn = $entries[0]['dn'];
                        // Kullanıcı bilgileriyle bağlan
                        $userBind = @ldap_bind($ldapConn, $userDn, $password);
                        if ($userBind) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
}
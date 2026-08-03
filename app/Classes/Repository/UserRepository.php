<?php
namespace App\Repository;

/**
 * @author Jochen Ott
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 */

use App\Model\User;
use Symfony\Component\Ldap\Ldap;

/**
 * Verwaltet die Benutzerdatenbank
 */
class UserRepository
{
    private Ldap $ldap;

    public function __construct(
        string $ldapHost,
        private string $ldapBindDn,
        private string $ldapBindPassword,
        private string $ldapPeopleDn,
    ) {
        $this->ldap = Ldap::create('ext_ldap', ['connection_string' => $ldapHost]);
        $this->bind();
    }

    private function bind()
    {
        $this->ldap->bind($this->ldapBindDn, $this->ldapBindPassword);
    }

    public function findOneByUserName(string $userName): ?User
    {
        if (!$userName) {
            return new User('unknown', 'unknown', '');
        }
        try {
            // employeeType == 1 <=> Benutzerkonto gesperrt
            $result = $this->ldap->query($this->ldapPeopleDn, '(&(objectclass=inetOrgPerson)(cn=' .
              ldap_escape($userName, '', LDAP_ESCAPE_FILTER) . ')(!(employeeType=1)))')->execute();
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return null;
        }
        if ($result[0]) {
            $entry = $result[0];
            $userName = $entry->getAttribute('cn')[0];
            $email = $entry->getAttribute('mail')[0];
            return new User($userName, $entry->getAttribute('givenName')[0] . ' ' . $entry->getAttribute('sn')[0], $email);
        } else {
            return new User($userName, $userName, '');
        }
    }
}

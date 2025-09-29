<?php
namespace App\Repository;

/**
 * @author Jochen Ott
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 */

use App\Model\User;
use App\Service\EmailService;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\Exception\InvalidCredentialsException;

/**
 * Verwaltet die Benutzerdatenbank
 */
class UserRepository
{
    private $ldap = null;

    public function __construct(
        private EmailService $emailService,
        string $ldapHost,
        private string $ldapBindDn,
        private string $ldapBindPassword,
        private string $ldapPeopleDn,
        private string $ldapGroupsDn,
    ) {
        $this->ldap = Ldap::create('ext_ldap', ['connection_string' => $ldapHost]);
        $this->bind();
    }

    private function bind()
    {
        $this->ldap->bind($this->ldapBindDn, $this->ldapBindPassword);
    }

    private function checkPassword(string $userName, string $password): bool
    {
        $passwordCorrect = true;
        try {
            $this->ldap->bind($this->getDnByUsername($userName), $password);
        } catch (InvalidCredentialsException $e) {
            $passwordCorrect = false;
        }
        $this->bind();
        return $passwordCorrect;
    }

    private function hasAufnahmeRole(string $userName)
    {
        $query = '(&(objectclass=groupOfNames)(cn=aufnahme)(member=' . $this->getDnByUserName($userName) . '))';
        $entry = $this->ldap->query($this->ldapGroupsDn, $query)->execute()[0];
        return !empty($entry);
    }

    private function getDnByUserName(string $userName): string
    {
        return 'cn=' . ldap_escape($userName) . ',' . $this->ldapPeopleDn;
    }

    /**
     * Gibt einen nicht-deaktivierten Benutzer zu gegebenen Credentials zurück
     *
     * @param string $userName
     * @param string $password im Klartext
     * @return User|null falls gefunden
     */
    public function findOneByCredentials(string $userName, string $password): ?User
    {
        if (!$userName || !$password || !$this->checkPassword($userName, $password)) {
            return null;
        }
        $user = $this->findOneByUserName($userName);
        if (!$user->hasAufnahmeRole()) {
            return null;
        }
        return $user;
    }

    /**
     * @param bool $skipRoleCheck: skip the check if the user has the "aufnahme" role because it is already known that the user has the role
     */
    public function findOneByUserName(string $userName, bool $skipRoleCheck = false): ?User
    {
        if (!$userName) {
            return new User('unknown', 'unknown', '', false);
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
            $hasRole = $skipRoleCheck ? true : $this->hasAufnahmeRole($userName);
            return new User($userName, $entry->getAttribute('givenName')[0] . ' ' . $entry->getAttribute('sn')[0], $email, $hasRole);
        } else {
            return new User($userName, $userName, '', false);
        }
    }

    /**
     * Gibt ein Array mit allen Benutzern zurück
     *
     * @return User[]
     */
    public function findAll()
    {
        $result = $this->ldap->query($this->ldapGroupsDn, '(cn=aufnahme)')->execute();

        $members = array_map(function ($dn) {
            if (substr($dn, 0, strlen('cn=')) !== 'cn=') {
                return null;
            }
            if (substr($dn, -strlen($this->ldapPeopleDn)) !== $this->ldapPeopleDn) {
                return null;
            }
            $userName = substr(substr($dn, strlen('cn=')), 0, -1-strlen($this->ldapPeopleDn));
            return $this->findOneByUserName($userName);
        }, $result[0]->getAttribute('member'));

        return array_filter($members, function ($entry) {
            return $entry !== null;
        });
    }

    public function sendEmailToAll(string $subject, string $body): void
    {
        $users = $this->findAll();
        foreach ($users as $user) {

            $emailAddress = $user->getEmailAddress();
            if (!$emailAddress) {
                continue;
            }
            $this->emailService->send($emailAddress, $subject, $body);
        }
    }
}

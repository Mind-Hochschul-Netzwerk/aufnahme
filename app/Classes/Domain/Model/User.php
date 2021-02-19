<?php
namespace MHN\Aufnahme\Domain\Model;

/**
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 */

use MHN\Aufnahme\Service\PasswordManager;

/**
 * Repräsentiert einen Benutzer
 */
class User
{
    /** @var int */
    private $id = 0;

    /** @var string */
    private $userName = '';

    /** @var string */
    private $passwordHash = '';

    /** @var string */
    private $realName = '';

    /**
     * Gibt die ID zurück
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setzt die Id
     *
     * @param int $id
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Gibt den Benutzernamen zurück
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Prüft das Format des Benutzernamens
     *
     * @param string $userName
     *
     * @return bool ob $userName gültig ist
     */
    public static function isUserNameValid($userName)
    {
        return (bool)preg_match('/^[A-Za-z0-9._-]{3,}$/', $userName);
    }

    /**
     * Setzt den Benutzernamen
     *
     * @param string $userName
     *
     * @return void
     *
     * @throws \UnexpectedValueException falls der Benutzername ein ungültiges Format hat
     */
    public function setUserName($userName)
    {
        if (!self::isUserNameValid($userName)) {
            throw new \UnexpectedValueException(
                'Benutzername ' . $userName . ' ungültig. Er muss vorher geprüft werden.',
                1491052831
            );
        }
        $this->userName = $userName;
    }

    /**
     * Gibt den Passworthash zurück.
     *
     * @return string
     */
    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    /**
     * Setzt den Passworthash.
     *
     * @param string $passwordHash
     *
     * @return void
     */
    public function setPasswordHash($passwordHash)
    {
        $this->passwordHash = $passwordHash;
    }

    /**
     * Setzt ein neues Passwort.
     *
     * @param string $password im Klartext
     *
     * @return void
     */
    public function setPassword($password)
    {
        $this->passwordHash = PasswordManager::getInstance()->hash($password);
    }

    /**
     * Gibt den Realnamen zurück
     *
     * @return string
     */
    public function getRealName()
    {
        return $this->realName;
    }

    /**
     * Setzt den Realnamen.
     *
     * @param string $realName
     *
     * @return void
     */
    public function setRealName($realName)
    {
        $this->realName = $realName;
    }
}

<?php
namespace MHN\Aufnahme\Domain\Repository;

/**
 * @author Jochen Ott
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 */

use MHN\Aufnahme\Domain\Model\User;
use MHN\Aufnahme\Service\PasswordManager;
use MHN\Aufnahme\Sql;

/**
 * Verwaltet die Benutzerdatenbank
 */
class UserRepository implements \MHN\Aufnahme\Interfaces\Singleton
{
    use \MHN\Aufnahme\Traits\Singleton;

    /** @var string */
    const TABLE_NAME = 'users';

    /** @var Sql */
    private $sql = null;

    /**
     * Instanziiert das Objekt
     *
     * Wird nur durch Interfaces\Singleton::getInstance() aufgerufen
     */
    private function __construct()
    {
        $this->sql = Sql::getInstance();
    }

    /**
     * Gibt einen nicht-deaktivierten Benutzer zu gegebenen Credentials zurück
     *
     * @param string $userName
     * @param string $password im Klartext
     * @return User|null falls gefunden
     */
    public function findOneByCredentials($userName, $password)
    {
        $user = $this->findOneByName($userName);

        if ($user !== null
            && PasswordManager::getInstance()->validate($user->getPasswordHash(), $password)
        ) {
            return $user;
        } else {
            return null;
        }
    }

    /**
     * Gibt einen Benutzer zu einem Benutzernamen zurück
     *
     * @param string $userName
     * @return User|null falls gefunden
     */
    public function findOneByName($userName)
    {
        $where = sprintf('username="%s"', $this->sql->escape($userName));
        return $this->findOneByWhere($where);
    }

    /**
     * Gibt den Benutzer mit der gegebenen ID zurück
     *
     * @param int $id
     * @return User|null
     */
    public function findOneById($id)
    {
        return $this->findOneByWhere('userid=' . $id);
    }

    /**
     * Gibt einen Benutzer zu einer WHERE-Bedingung zurück.
     *
     * @param string $where
     * @return User|null
     */
    private function findOneByWhere($where)
    {
        $result = $this->sql->select(self::TABLE_NAME, '*', $where);

        if ($result->num_rows === 0) {
            return null;
        }

        return $this->createUserObject($result->fetch_assoc());
    }

    /**
     * Gibt ein Array mit allen Benutzern zurück
     *
     * @return User[]
     */
    public function findAll()
    {
        $result = $this->sql->select(self::TABLE_NAME, '*');

        $users = [];
        while (($row = $result->fetch_assoc())) {
            $users[] = $this->createUserObject($row);
        }

        return $users;
    }

    /**
     * Erstellt ein User-Objekt mit den angegeben Daten
     *
     * @param mixed[] $row Datensatz aus der Datenbank
     * @return User
     */
    private function createUserObject(array $row)
    {
        $user = new User();
        $user->setId((int)$row['userid']);
        $user->setUserName($row['username']);
        $user->setRealName($row['realname']);
        $user->setPasswordHash($row['password']);
        return $user;
    }

    /**
     * Speichert einen Benutzer in der Datenbank.
     *
     * Falls der Benutzer noch nicht in der Datenbank steht, wird der Datensatz
     * neu angelegt und die ID gesetzt. Ansonsten wird der Datensatz aktualisiert.
     *
     * @param User $user
     * @return void
     */
    public function save(User $user)
    {
        if ($user->getId() !== 0) {
            $this->update($user);
        } else {
            $this->create($user);
        }
    }

    /**
     * Legt einen neuen Benutzerdatensatz in der Datenbank an.
     *
     * @param User $user
     * @return void
     */
    private function create(User $user)
    {
        $data = [
            'username' => $user->getUserName(),
            'realname' => $user->getRealName(),
            'password' => $user->getPasswordHash(),
        ];

        $id = $this->sql->insert(self::TABLE_NAME, $data);
        $user->setId($id);
    }

    /**
     * Aktualisiert einen Benutzerdatensatz in der Datenbank.
     *
     * @param User $user
     * @return void
     */
    private function update(User $user)
    {
        $data = [
            'username' => $user->getUserName(),
            'realname' => $user->getRealName(),
            'password' => $user->getPasswordHash(),
        ];

        $this->sql->update(self::TABLE_NAME, $data, 'userid=' . $user->getId());
    }

    /**
     * Löscht einen Benutzer in der Datenbank
     */
    public function delete(User $user)
    {
        $this->sql->delete(self::TABLE_NAME, 'userid=' . $user->getId());
    }
}

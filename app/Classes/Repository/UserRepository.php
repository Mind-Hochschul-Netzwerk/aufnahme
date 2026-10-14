<?php
namespace App\Repository;

/**
 * @author Jochen Ott
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 */

use App\Model\User;
use Hengeb\Db\Db;

/**
 * Verwaltet die Benutzer, die sich schon einmal per OIDC eingeloggt haben.
 *
 * Es gibt kein eigenes Verzeichnis aller Mitglieder der Aufnahmekommission: wer Zugriff
 * hat, entscheidet allein Authelia/OIDC. Diese Tabelle dient nur dazu, zu bereits bekannten
 * Usernamen (aus Voten/Mails/Statusänderungen) einen Klarnamen anzuzeigen.
 */
class UserRepository
{
    public function __construct(
        private Db $db,
    ) {}

    public function findOneByUserName(string $userName): ?User
    {
        if (!$userName) {
            return new User('unknown', 'unknown');
        }

        $row = $this->db->query('SELECT * FROM users WHERE username = :username', [
            'username' => $userName,
        ])->getRow();

        return new User($userName, $row ? $row['realName'] : $userName);
    }

    /**
     * Legt den Nutzer an oder aktualisiert den Klarnamen, z.B. bei jedem Login per OIDC.
     */
    public function upsert(User $user): void
    {
        $this->db->query(
            'INSERT INTO users (username, realName) VALUES (:username, :realName)
             ON DUPLICATE KEY UPDATE realName = VALUES(realName)',
            ['username' => $user->getUserName(), 'realName' => $user->getRealName()],
        );
    }

    /**
     * Löscht Einträge, auf die keine Voten, Mails oder Statusänderungen mehr verweisen.
     */
    public function deleteOrphans(): void
    {
        $this->db->query('DELETE FROM users WHERE username NOT IN (
            SELECT username FROM voten WHERE username IS NOT NULL
            UNION SELECT username FROM mails WHERE username IS NOT NULL
            UNION SELECT statusaenderung_username FROM antraege WHERE statusaenderung_username IS NOT NULL
        )');
    }
}

<?php
namespace App\Repository;

use App\Model\Antrag;
use Hengeb\Db\Db;

class AntragRepository implements \App\Interfaces\Singleton
{
    use \App\Traits\Singleton;

    /**
     * Lädt die Daten aus der Datenbank
     *
     * @param int $id
     * @throws \InvalidArgumentException Antrag-ID ist ungültig
     */
    public function getOneById(int $id): Antrag
    {
        $row = Db::getInstance()->query('SELECT * FROM antraege WHERE antrag_id = :antrag_id', [
            'antrag_id' => $id
        ])->getRow();

        return $row ? Antrag::fromDatabase($row) : throw new \OutOfBoundsException('Antrag-ID ungültig: ' . $id, 1490568194);
    }

    public function findOneByEmail(string $email): ?Antrag
    {
        $row = Db::getInstance()->query('SELECT * FROM antraege WHERE mail = :mail', [
            'mail' => $email,
        ])->getRow();

        return $row ? Antrag::fromDatabase($row) : null;
    }

    /**
     * Speichert einen neuen Antrag.
     *
     * @throws \RuntimeException
     */
    public function add(Antrag $antrag): void
    {
        $row = [
            'status' => $antrag->getStatus(),
            'ts_antrag' => $antrag->getTsAntrag(),
            'ts_nachfrage' => $antrag->getTsNachfrage(),
            'ts_antwort' => $antrag->getTsAntwort(),
            'ts_entscheidung' => $antrag->getTsEntscheidung(),
            'ts_statusaenderung' => $antrag->getTsStatusaenderung(),
            'statusaenderung_username' => $antrag->getStatusaenderungUserName(),
            'bemerkung' => $antrag->getBemerkung(),
            'kommentare' => $antrag->getKommentare(),
            'formData' => $antrag->getDaten()->json(),
            'mail' => $antrag->getDaten()->getEmail(),
            'ts_erinnerung' => 0,
        ];

        $id = Db::getInstance()->query('INSERT INTO antraege SET ' . implode(', ', array_map(
            fn($key) => "$key = :$key", array_keys($row)
        )), $row)->getInsertId();
        $antrag->setId($id);
    }

    public function getAllByStatus(int $status): array
    {
        $rows = Db::getInstance()->query('SELECT * FROM antraege WHERE status = :status ORDER BY ts_entscheidung, ts_antrag', [
            'status' => $status,
        ])->getAll();
        return array_map(fn($row) => Antrag::fromDatabase($row), $rows);
    }

    public function alleOffenenAntraege()
    {
        return array_merge(
            self::getAllByStatus(Antrag::STATUS_NEU_BEWERTEN),
            self::getAllByStatus(Antrag::STATUS_BEWERTEN),
            self::getAllByStatus(Antrag::STATUS_NACHFRAGEN),
            self::getAllByStatus(Antrag::STATUS_AUFNEHMEN),
            self::getAllByStatus(Antrag::STATUS_ABLEHNEN),
            self::getAllByStatus(Antrag::STATUS_AUF_ANTWORT_WARTEN),
        );
    }

    public function alleEntschiedenenAntraege()
    {
        return array_merge(
            self::getAllByStatus(Antrag::STATUS_AUFGENOMMEN),
            self::getAllByStatus(Antrag::STATUS_ABGELEHNT),
            self::getAllByStatus(Antrag::STATUS_AKTIVIERT),
        );
    }

    //speichert den akt. Status
    public function save(Antrag $antrag)
    {
        $row = [
            'status' => $antrag->getStatus(),
            'ts_antrag' => $antrag->getTsAntrag(),
            'ts_nachfrage' => $antrag->getTsNachfrage(),
            'ts_antwort' => $antrag->getTsAntwort(),
            'ts_entscheidung' => $antrag->getTsEntscheidung(),
            'bemerkung' => $antrag->getBemerkung(),
            'kommentare' => $antrag->getKommentare(),
            'ts_statusaenderung' => $antrag->getTsStatusaenderung(),
            'statusaenderung_username' => $antrag->getStatusaenderungUserName(),
            'ts_erinnerung' => $antrag->getTsErinnerung(),
            'formData' => $antrag->getDaten()->json(),
            'mail' => $antrag->getDaten()->getEmail(),
        ];

        Db::getInstance()->query('UPDATE antraege SET ' . implode(', ', array_map(
            fn($key) => "$key = :$key", array_keys($row)
        )) . ' WHERE antrag_id = :antrag_id', [...$row, 'antrag_id' => $antrag->getId()]);
    }

    /**
     * Löscht alle alten Anträge
     */
    public function deleteOld()
    {
        // aktivierte Benutzerkonten nach 8 Wochen löschen
        Db::getInstance()->query('DELETE FROM antraege WHERE status = :status AND UNIX_TIMESTAMP()-ts_entscheidung > 3600*24*7*8', [
            'status' => Antrag::STATUS_AKTIVIERT
        ]);

        // nicht aktivierte Benutzerkonten nach 12 Wochen löschen
        Db::getInstance()->query('DELETE FROM antraege WHERE status = :status AND UNIX_TIMESTAMP()-ts_entscheidung > 3600*24*7*12', [
            'status' => Antrag::STATUS_AUFGENOMMEN
        ]);

        // abgelehnte Anträge nach 12 Wochen löschen
        Db::getInstance()->query('DELETE FROM antraege WHERE status = :status AND UNIX_TIMESTAMP()-ts_entscheidung > 3600*24*7*12', [
            'status' => Antrag::STATUS_ABGELEHNT
        ]);

        // verwaiste Mails und Voten löschen
        EmailRepository::getInstance()->deleteOrphans();
        VoteRepository::getInstance()->deleteOrphans();
    }
}

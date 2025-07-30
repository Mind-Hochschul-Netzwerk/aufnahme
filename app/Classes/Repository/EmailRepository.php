<?php
namespace App\Repository;

/**
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

use DateTime;
use App\Model\Antrag;
use App\Model\Email;
use Hengeb\Db\Db;

/**
 * Verwaltet das E-Mail-Archiv
 */
class EmailRepository implements \App\Interfaces\Singleton
{
    use \App\Traits\Singleton;

    /**
     * Gibt alle E-Mail-Objekte zu einem Antrag zurück
     *
     * @param Antrag $antrag
     * @return Email[]
     */
    public function findAllByAntrag(Antrag $antrag): array
    {
        $rows = Db::getInstance()->query('SELECT * FROM mails WHERE antrag_id = :antrag_id ORDER BY ts DESC',
            ['antrag_id' =>  $antrag->getId()])->getAll();

        return array_map(fn($row) => $this->createEmailObject($row), $rows);
    }

    public function findOneByAntragAndTimestamp(Antrag $antrag, int $timestamp): ?Email
    {
        $row = Db::getInstance()->query('SELECT * FROM mails WHERE antrag_id = :antrag_id AND ts = :ts', [
            'antrag_id' =>  $antrag->getId(),
            'ts' => $timestamp
        ])->getRow();

        return $row ? $this->createEmailObject($row) : null;
    }

    /**
     * Erstellt ein E-Mail-Objekt mit den angegeben Daten
     *
     * @param mixed[] $row Datensatz aus der Datenbank
     * @return Email
     */
    private function createEmailObject(array $row)
    {
        $email = new Email();
        $email->setAntragId((int)$row['antrag_id']);
        $email->setGrund($row['grund']);
        $email->setSenderUserName((string)$row['username']);
        $email->setCreationTime(new DateTime('@' . $row['ts']));
        $email->setSubject($row['mailsubject']);
        $email->setText($row['mailtext']);
        return $email;
    }

    /**
     * Speichert ein E-Mail in der Datenbank.
     *
     * @param Email $email
     * @return void
     */
    public function add(Email $email)
    {
        $data = [
            'antrag_id' => $email->getAntragId(),
            'grund' => $email->getGrund(),
            'username' => $email->getSenderUserName(),
            'ts' => $email->getCreationTime()->getTimestamp(),
            'mailsubject' => $email->getSubject(),
            'mailtext' => $email->getText(),
        ];

        Db::getInstance()->query('INSERT INTO mails SET ' . implode(', ', array_map(
            fn($key) => "$key = :$key", array_keys($data)
        )), $data);
    }

    public function deleteOrphans(): void
    {
        Db::getInstance()->query('DELETE FROM mails WHERE (SELECT a.antrag_id FROM antraege a WHERE a.antrag_id = mails.antrag_id) IS NULL');
    }
}

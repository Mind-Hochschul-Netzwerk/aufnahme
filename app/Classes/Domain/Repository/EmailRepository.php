<?php
namespace MHN\Aufnahme\Domain\Repository;

/**
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

use DateTime;
use MHN\Aufnahme\Antrag;
use MHN\Aufnahme\Domain\Model\Email;
use MHN\Aufnahme\Sql;

/**
 * Verwaltet das E-Mail-Archiv
 */
class EmailRepository implements \MHN\Aufnahme\Interfaces\Singleton
{
    use \MHN\Aufnahme\Traits\Singleton;

    /** @var string */
    const TABLE_NAME = 'mails';

    /** @var Sql */
    private $sql = null;

    /**
     * Instanziierung nur durch Interfaces\Singleton::getInstance() aufgerufen
     */
    private function __construct()
    {
        $this->sql = Sql::getInstance();
    }

    /**
     * Gibt alle E-Mail-Objekte zu einem Antrag zurück
     *
     * @param Antrag $antrag
     * @return Email[]
     */
    public function findByAntrag(Antrag $antrag)
    {
        $result = $this->sql->select(self::TABLE_NAME, '*', 'antrag_id = ' . $antrag->getId() . ' ORDER BY ts DESC');

        $emails = [];
        while (($row = $result->fetch_assoc())) {
            $emails[] = $this->createEmailObject($row);
        }

        return $emails;
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
        $email->setSenderUserName($row['username']);
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

        $this->sql->insert(self::TABLE_NAME, $data);
    }
}

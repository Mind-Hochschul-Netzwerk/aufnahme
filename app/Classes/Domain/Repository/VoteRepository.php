<?php
namespace App\Domain\Repository;

/**
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

use DateTime;
use App\Antrag;
use App\Domain\Model\User;
use App\Domain\Model\Vote;
use App\Sql;

/**
 * Verwaltet die Voten in der Datenbank
 */
class VoteRepository implements \App\Interfaces\Singleton
{
    use \App\Traits\Singleton;

    /** @var string */
    const TABLE_NAME = 'voten';

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
     * Gibt alle Vote-Objekte zu einem Antrag zurück
     *
     * @param Antrag $antrag
     *
     * @return Vote[]
     */
    public function findByAntrag(Antrag $antrag)
    {
        $result = $this->sql->select(self::TABLE_NAME, '*', 'antrag_id = ' . $antrag->getId() . ' ORDER BY ts DESC');

        $votes = [];
        while (($row = $result->fetch_assoc())) {
            $votes[] = $this->createVoteObject($row);
        }

        return $votes;
    }

    /**
     * Gibt die Vote-Objekte zu einer Antrag-ID zurück, aber nur das neuste
     * pro User.
     *
     * @param Antrag $antrag
     *
     * @return Vote[] assoziatives Array [UserName => Vote-Value, ...]
     */
    public function findLatestByAntrag(Antrag $antrag)
    {
        $votes = $this->findByAntrag($antrag);

        $latestVotes = [];
        foreach ($votes as $vote) {
            if (isset($latestVotes[$vote->getUserName()])) {
                continue;
            }
            $latestVotes[$vote->getUserName()] = $vote;
        }

        return $latestVotes;
    }

    /**
     * Erstellt ein Vote-Objekt mit den angegeben Daten
     *
     * @param mixed[] $row Datensatz aus der Datenbank
     *
     * @return Vote
     */
    private function createVoteObject(array $row)
    {
        $vote = new Vote();
        $vote->setAntragId((int)$row['antrag_id']);
        $vote->setUserName((string)$row['username']);
        $vote->setTime(new DateTime('@' . $row['ts']));
        $vote->setValue((int)$row['votum']);
        $vote->setBemerkung($row['bemerkung']);
        $vote->setNachfrage($row['nachfrage']);
        return $vote;
    }

    /**
     * Speichert ein Vote in der Datenbank.
     *
     * @param Vote $vote
     *
     * @return void
     */
    public function add(Vote $vote)
    {
        $data = [
            'antrag_id' => $vote->getAntragId(),
            'votum' => $vote->getValue(),
            'username' => $vote->getUserName(),
            'ts' => $vote->getTime()->getTimestamp(),
            'bemerkung' => $vote->getBemerkung(),
            'nachfrage' => $vote->getNachfrage(),
        ];

        $this->sql->insert(self::TABLE_NAME, $data);
    }
}

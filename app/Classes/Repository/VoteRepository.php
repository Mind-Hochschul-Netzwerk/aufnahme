<?php
namespace App\Repository;

/**
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

use DateTime;
use App\Model\Antrag;
use App\Model\Vote;
use Hengeb\Db\Db;

/**
 * Verwaltet die Voten in der Datenbank
 */
class VoteRepository
{
    public function __construct(
        private Db $db,
        private UserRepository $userRepository,
    ) {}

    /**
     * Gibt alle Vote-Objekte zu einem Antrag zurück
     */
    public function findAllByAntrag(Antrag $antrag): array
    {
        $rows = $this->db->query('SELECT * FROM voten WHERE antrag_id = :antrag_id ORDER BY ts DESC',
            ['antrag_id' =>  $antrag->getId()])->getAll();

        return array_map(fn($row) => $this->createVoteObject($row), $rows);
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
        $votes = $this->findAllByAntrag($antrag);

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
        $vote = new Vote($this->userRepository);
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

        $this->db->query('INSERT INTO voten SET ' . implode(', ', array_map(
            fn($key) => "$key = :$key", array_keys($data)
        )), $data);
    }

    public function deleteOrphans(): void
    {
        $this->db->query('DELETE FROM voten WHERE (SELECT a.antrag_id FROM antraege a WHERE a.antrag_id = voten.antrag_id) IS NULL');
    }
}

<?php
namespace MHN\Aufnahme;

use MHN\Aufnahme\Domain\Model\Vote;
use MHN\Aufnahme\Daten;
use MHN\Aufnahme\Domain\Repository\UserRepository;
use MHN\Aufnahme\Domain\Repository\VoteRepository;
use MHN\Aufnahme\Domain\Repository\EmailRepository;

class Antrag
{
    /** @var string */
    const TABLE_NAME = 'antraege';

    const STATUS_NEU_BEWERTEN = -1;
    const STATUS_BEWERTEN = 0;
    const STATUS_NACHFRAGEN = 1;
    const STATUS_AUFNEHMEN = 2;
    const STATUS_ABLEHNEN = 3;
    const STATUS_AUF_ANTWORT_WARTEN = 4;
    const STATUS_AUFGENOMMEN = 5;
    const STATUS_ABGELEHNT = 6;
    const STATUS_AKTIVIERT = 7;

    /** @var Sql */
    private $sql = null;

    private $antrag_id;

    private $status = self::STATUS_BEWERTEN;

    private $ts_antrag = 0;

    private $ts_nachfrage = 0;

    private $ts_antwort = 0;

    private $ts_entscheidung = 0;

    private $ts_erinnerung = 0;

    private $ts_statusaenderung = 0;

    private $statusaenderung_userName = '';

    private $bemerkung;

    private $kommentare;

    private $fragen_werte;

    /** @var Vote[] */
    private $votes = [];

    /** @var Vote[]|null */
    private $latestVotes = null;

    public $daten;

    /**
     * Lädt einen Antrag bzw. erzeugt einen neuen
     *
     * @param int|null $id
     * @throws \InvalidArgumentException Antrag-ID ist ungültig
     */
    public function __construct($id = null)
    {
        $this->sql = Sql::getInstance();

        if ($id !== null) {
            $this->loadFromDatabase($id);
        }
    }

    /**
     * Lädt die Daten aus der Datenbank
     *
     * @param int $id
     * @throws \InvalidArgumentException Antrag-ID ist ungültig
     * @return void|bool
     */
    private function loadFromDatabase($id)
    {
        assert(is_numeric($id));
        $row = Sql::queryToArraySingle($this->sql->select(self::TABLE_NAME, '*', 'antrag_id=' . $id));
        if ($row === null) {
            throw new \InvalidArgumentException('Antrag-ID ungültig: ' . $id, 1490568194);
        }

        $this->antrag_id = (int)$row['antrag_id'];
        $this->status = (int)$row['status'];
        $this->ts_antrag = $row['ts_antrag'];
        $this->ts_nachfrage = $row['ts_nachfrage'];
        $this->ts_erinnerung = $row['ts_erinnerung'];
        $this->ts_antwort = $row['ts_antwort'];
        $this->ts_entscheidung = $row['ts_entscheidung'];
        $this->ts_statusaenderung = $row['ts_statusaenderung'];
        $this->statusaenderung_userName = (string)$row['statusaenderung_username'];
        $this->bemerkung = $row['bemerkung'];
        $this->kommentare = $row['kommentare'];
        $this->fragen_werte = @unserialize($row['fragen_werte']);
        $this->daten = Daten::datenByAntragId($this->antrag_id);
        if ($this->daten === null) {
            die('Konnte Daten nicht laden zu Antrag Nr. ' . $this->antrag_id);
        }
        $this->votes = VoteRepository::getInstance()->findByAntrag($this);
    }

    //aus den Daten.
    public function getName()
    {
        return $this->daten->getName();
    }

    public function getVorname()
    {
        return $this->daten->getVorname();
    }

    public function getEMail()
    {
        return $this->daten->getEMail();
    }

    public function getId(): int
    {
        return $this->antrag_id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getKommentare()
    {
        return $this->kommentare;
    }

    public function setKommentare($k)
    {
        $this->kommentare = trim($k);
    }

    public function getFragenWerte()
    {
        return $this->fragen_werte;
    }

    public function setFragenWerte($f)
    {
        $this->fragen_werte = $f;
    }

    public function addKommentar($username, $kommentar)
    {
        //oben hinzufügen:
        $this->kommentare = trim($this->kommentare);
        $this->kommentare = 'Von ' . $username . ' am ' .
            Util::tsToDatum(time()) . ":\r\n" . trim($kommentar) . "\r\n\r\n" . $this->kommentare;
    }

    //der Status als Text
    public function getStatusReadable()
    {
        global $global_status;
        $s = $global_status[$this->status];
        if ($s == '') {
            $s = 'undefiniert';
        }
        return $s;
    }

    public function getTsErinnerung()
    {
        return $this->ts_erinnerung;
    }

    public function getTsStatusaenderung()
    {
        return $this->ts_statusaenderung;
    }

    public function getDatumStatusaenderung()
    {
        return Util::tsToDatum($this->ts_statusaenderung);
    }

    public function getTsAntrag()
    {
        return $this->ts_antrag;
    }

    //als Text ...
    public function getDatumAntrag()
    {
        return Util::tsToDatum($this->ts_antrag);
    }

    public function getDatumEntscheidung()
    {
        return Util::tsToDatum($this->ts_entscheidung);
    }

    public function getDatumAntwort()
    {
        return Util::tsToDatum($this->ts_antwort);
    }

    public function getDatumNachfrage()
    {
        return Util::tsToDatum($this->ts_nachfrage);
    }

    public function getBemerkung()
    {
        return $this->bemerkung;
    }

    /**
     * Gibt den Benutzernamen zurück, von dem die letzte Statusänderung stammt.
     *
     * @return string Benutzername; "system", falls der Status nie geändert wurde.
     */
    public function getStatusaenderungUserName(): string
    {
        return $this->statusaenderung_userName ? $this->statusaenderung_userName : "system";
    }

    //gibt 'niedrig', 'mittel' oder 'hoch' zurÃ¼ck, je nach
    //ts_antrag. Grenze bei 2 bzw. 4 Wochen.
    public function getDringlichkeit()
    {
        if ($this->status == self::STATUS_AUF_ANTWORT_WARTEN) {
            return 'warten';
        }
        $diff = time() - $this->ts_antrag;
        if ($diff < 60 * 60 * 24 * 14) {
            return 'niedrig';
        }
        if ($diff < 60 * 60 * 24 * 28) {
            return 'mittel';
        }
        return 'hoch';
    }

    /**
     * Ändert den Status
     *
     * @param $status neuer Status
     * @param $userName
     */
    public function setStatus(int $status, string $userName): void
    {
        global $global_status;
        assert(in_array($status, array_keys($global_status), true));
        if ($this->status === $status) {
            return;
        }
        $this->status = $status;
        $this->ts_statusaenderung = time();
        $this->statusaenderung_username = $userName;
    }

    public function setBemerkung($bem)
    {
        $this->bemerkung = $bem;
    }

    public function setTsNachfrage($ts)
    {
        assert(is_numeric($ts));
        $this->ts_nachfrage = $ts;
    }

    public function setTsAntrag($ts)
    {
        assert(is_numeric($ts));
        $this->ts_antrag = $ts;
    }

    public function setTsAntwort($ts)
    {
        assert(is_numeric($ts));
        $this->ts_antwort = $ts;
    }

    public function setTsErinnerung($ts)
    {
        assert(is_numeric($ts));
        $this->ts_erinnerung = $ts;
    }

    public function setTsEntscheidung($ts)
    {
        assert(is_numeric($ts));
        $this->ts_entscheidung = $ts;
    }

    /**
     * setzt die inhaltlichen Daten zum Antrag
     *
     * @param Daten $daten
     * @return void
     */
    public function setDaten(Daten $daten)
    {
        $this->daten = $daten;
    }

    public function getDaten(): Daten
    {
        return $this->daten;
    }

    /**
     * Speichert einen neuen Antrag.
     *
     * @return bool Erfolg
     */
    public function addThisAntrag()
    {
        $this->sql->startTransaction();

        $antrag = [
            'status' => $this->status,
            'ts_antrag' => $this->ts_antrag,
            'ts_nachfrage' => $this->ts_nachfrage,
            'ts_antwort' => $this->ts_antwort,
            'ts_entscheidung' => $this->ts_entscheidung,
            'ts_statusaenderung' => $this->ts_statusaenderung,
            'statusaenderung_username' => $this->statusaenderung_userName,
            'bemerkung' => (string)$this->bemerkung,
            'kommentare' => (string)$this->kommentare,
            'fragen_werte' => serialize($this->fragen_werte),
            'ts_erinnerung' => 0,
        ];

        try {
            $id = $this->sql->insert(self::TABLE_NAME, $antrag);
            $this->daten->addThisDaten($id);
            $this->antrag_id = $id;
        } catch (\RuntimeException $e) {
            $this->sql->rollback();
            return false;
        }

        $this->sql->commit();

        return true;
    }

    /**
     * Gibt zurück, ob der Antrag "grün", also zum Annehmen, ist, d.h.:
     *  - 3 Ja-Voten
     *  - kein Nachfragen
     *  - kein Nein
     *
     * @return bool
     */
    public function getGruen()
    {
        $anzahlJa = 0;
        foreach (VoteRepository::getInstance()->findLatestByAntrag($this) as $vote) {
            $voteValue = $vote->getValue();
            if ($voteValue === Vote::JA) {
                ++$anzahlJa;
            } elseif ($voteValue === Vote::NEIN || $voteValue === Vote::NACHFRAGEN) {
                return false;
            }
        }
        return $anzahlJa >= 3;
    }

    //gibt einen array aller Voten, sortiert nach Zeit, zurueck.
    public function getVotes()
    {
        return $this->votes;
    }

    public function getLatestVoteByUserName(string $userName): ?Vote
    {
        if ($this->latestVotes === null) {
            $this->latestVotes = VoteRepository::getInstance()->findLatestByAntrag($this);
        }
        if (!isset($this->latestVotes[$userName])) {
            return null;
        }
        return $this->latestVotes[$userName];
    }

    /**
     * Gibt das letzte Abstimmungsverhalten zu einem Benutzer als lesbare Kurzfassung zurück.
     */
    public function getLatestVoteReadableByUserName(string $userName): string
    {
        $vote = $this->getLatestVoteByUserName($userName);
        if ($vote === null) {
            return '--';
        }
        return $vote->getValueReadable();
    }

    /**
     * Gibt die CSS-Farbklasse für das letzte Abstimmungsverhalten zu einem Benutzer zurück.
     */
    public function getLatestVoteColorByUserName(string $userName): string
    {
        $vote = $this->getLatestVoteByUserName($userName);
        if ($vote === null) {
            return 'antrag_bewertung_weiss';
        }
        return $vote->getValueColor();
    }

    //alle, die nicht den Status aufgenommen oder abgelehnt haben.
    public static function alleOffenenAntraege()
    {
        $antraege = Sql::queryToArray(Sql::getInstance()->select(self::TABLE_NAME, 'antrag_id',
            'status NOT IN ( ' . implode(',', [self::STATUS_AUFGENOMMEN, self::STATUS_ABGELEHNT, self::STATUS_AKTIVIERT]) . ') ORDER BY ts_antrag'
        ));
        $result = [];
        foreach ($antraege as $a) {
            array_push($result, new Antrag($a['antrag_id']));
        }
        return $result;
    }

    public static function alleEntschiedenenAntraege()
    {
        $antraege = Sql::queryToArray(Sql::getInstance()->select(self::TABLE_NAME, 'antrag_id',
            'status IN ( ' . implode(',', [self::STATUS_AUFGENOMMEN, self::STATUS_ABGELEHNT, self::STATUS_AKTIVIERT]) . ') ORDER BY ts_entscheidung'
        )
        );
        $result = [];
        foreach ($antraege as $a) {
            array_push($result, new Antrag($a['antrag_id']));
        }
        return $result;
    }

    //speichert den akt. Status
    public function save()
    {
        $result = '';
        $antrag_neu = [
            'status' => $this->status,
            'ts_antrag' => $this->ts_antrag,
            'ts_nachfrage' => $this->ts_nachfrage,
            'ts_antwort' => $this->ts_antwort,
            'ts_entscheidung' => $this->ts_entscheidung,
            'bemerkung' => $this->bemerkung,
            'kommentare' => $this->kommentare,
            'ts_statusaenderung' => $this->ts_statusaenderung,
            'statusaenderung_username' => $this->statusaenderung_userName,
            'ts_erinnerung' => $this->ts_erinnerung,
            'fragen_werte' => @serialize($this->fragen_werte),
        ];
        if ($this->sql->update(self::TABLE_NAME, $antrag_neu, 'antrag_id=' . $this->antrag_id)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Löscht alle alten Anträge
     *   - angenommene Anträge, die in die Mitgliederdatenbank übernommen wurden, 8 Wochen nach der Entscheidung
     *   - angenommene Anträge, die nicht in die Mitgliederdatenbank übernommen wurden, nach 366 Tagen
     *   - abgelehnte Anträge nach 60 Wochen (Einspruchsmöglichkeit bis zur Mitgliederversammlung)
     */
    public function deleteOld()
    {
        // aktivierte Benutzerkonten
        Sql::getInstance()->delete(self::TABLE_NAME, 'status=' . self::STATUS_AKTIVIERT . ' AND UNIX_TIMESTAMP()-ts_entscheidung > 3600*24*7*8');

        // nicht aktivierte Benutzerkonten
        Sql::getInstance()->delete(self::TABLE_NAME, 'status=' . self::STATUS_AUFGENOMMEN . ' AND UNIX_TIMESTAMP()-ts_entscheidung > 3600*24*365');

        // abgelehnte Anträge
        Sql::getInstance()->delete(self::TABLE_NAME, 'status=' . self::STATUS_ABGELEHNT . ' AND UNIX_TIMESTAMP()-ts_entscheidung > 3600*24*7*60');

        // Daten, Mails und Voten löschen
        Sql::getInstance()->delete(Daten::TABLE_NAME, '(SELECT a.antrag_id FROM ' . self::TABLE_NAME . ' a WHERE a.antrag_id = ' . Daten::TABLE_NAME . '.antrag_id) IS NULL');
        Sql::getInstance()->delete(EmailRepository::TABLE_NAME, '(SELECT a.antrag_id FROM ' . self::TABLE_NAME . ' a WHERE a.antrag_id = ' . EmailRepository::TABLE_NAME . '.antrag_id) IS NULL');
        Sql::getInstance()->delete(VoteRepository::TABLE_NAME, '(SELECT a.antrag_id FROM ' . self::TABLE_NAME . ' a WHERE a.antrag_id = ' . VoteRepository::TABLE_NAME . '.antrag_id) IS NULL');

    }
}

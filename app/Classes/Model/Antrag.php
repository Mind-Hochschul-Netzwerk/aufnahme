<?php
namespace App\Model;

use App\Model\Vote;
use App\Model\FormData;
use App\Repository\VoteRepository;
use App\Util;
use Hengeb\Token\Token;

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

    const STATUS_READABLE = [
        self::STATUS_NEU_BEWERTEN => 'Neu bewerten',
        self::STATUS_BEWERTEN  => 'Bewerten',
        self::STATUS_NACHFRAGEN  => 'Nachfragen',
        self::STATUS_AUFNEHMEN => 'Aufnehmen',
        self::STATUS_ABLEHNEN => 'Ablehnen',
        self::STATUS_AUF_ANTWORT_WARTEN => 'Auf Antwort warten',
        self::STATUS_AUFGENOMMEN => 'Aktivierungslink verschickt',
        self::STATUS_AKTIVIERT => 'Mitgliedskonto aktiviert',
        self::STATUS_ABGELEHNT => 'Abgelehnt',
    ];

    private int $antrag_id = 0;
    private int $status = self::STATUS_BEWERTEN;
    private int $ts_antrag = 0;
    private int $ts_nachfrage = 0;
    private int $ts_antwort = 0;
    private int $ts_entscheidung = 0;
    private int $ts_erinnerung = 0;
    private int $ts_statusaenderung = 0;
    private string $statusaenderung_userName = '';
    private string $bemerkung = '';
    private string $kommentare = '';

    /** @var Vote[] */
    private array $votes = [];

    /** @var Vote[]|null */
    private ?array $latestVotes = null;

    private FormData $daten;

    public function __construct()
    {
        $this->daten = new FormData();
    }

    public static function fromDatabase(array $row): static
    {
        $instance = new static();
        $instance->antrag_id = (int)$row['antrag_id'];
        $instance->status = (int)$row['status'];
        $instance->ts_antrag = $row['ts_antrag'];
        $instance->ts_nachfrage = $row['ts_nachfrage'];
        $instance->ts_erinnerung = $row['ts_erinnerung'];
        $instance->ts_antwort = $row['ts_antwort'];
        $instance->ts_entscheidung = $row['ts_entscheidung'];
        $instance->ts_statusaenderung = $row['ts_statusaenderung'];
        $instance->statusaenderung_userName = (string)$row['statusaenderung_username'];
        $instance->bemerkung = $row['bemerkung'];
        $instance->kommentare = $row['kommentare'];
        $instance->daten = new formData($row['formData']);
        $instance->votes = VoteRepository::getInstance()->findAllByAntrag($instance);
        return $instance;
    }

    //aus den Daten.
    public function getName(): string
    {
        return $this->daten->getName();
    }

    public function getVorname(): string
    {
        return $this->daten->getVorname();
    }

    public function getEMail(): string
    {
        return $this->daten->getEMail();
    }

    public function getId(): int
    {
        return $this->antrag_id;
    }

    public function setId(int $id): void
    {
        $this->antrag_id = $id;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getKommentare(): string
    {
        return $this->kommentare;
    }

    public function setKommentare(string $k): void
    {
        $this->kommentare = trim($k);
    }

    public function addKommentar(string $username, string $kommentar): void
    {
        //oben hinzufügen:
        $this->kommentare = trim($this->kommentare);
        $this->kommentare = 'Von ' . $username . ' am ' .
            Util::tsToDatum(time()) . ":\r\n" . trim($kommentar) . "\r\n\r\n" . $this->kommentare;
    }

    public function getStatusReadable(): string
    {
        return self::STATUS_READABLE[$this->status] ?? 'undefiniert';
    }

    public function getDatumStatusaenderung(): string
    {
        return Util::tsToDatum($this->ts_statusaenderung);
    }

    //als Text ...
    public function getDatumAntrag(): string
    {
        return Util::tsToDatum($this->ts_antrag);
    }

    public function getDatumEntscheidung(): string
    {
        return Util::tsToDatum($this->ts_entscheidung);
    }

    public function getDatumAntwort(): string
    {
        return Util::tsToDatum($this->ts_antwort);
    }

    public function getDatumNachfrage(): string
    {
        return Util::tsToDatum($this->ts_nachfrage);
    }

    public function getBemerkung(): string
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

    //gibt 'niedrig', 'mittel' oder 'hoch' zurück, je nach
    //ts_antrag. Grenze bei 2 bzw. 4 Wochen.
    public function getDringlichkeit(): string
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
        if (!isset(self::STATUS_READABLE[$status])) {
            throw OutOfBoundsException('invalid status: ' . $status, 1753568815);
        }

        if ($this->status === $status) {
            return;
        }
        $this->status = $status;
        $this->ts_statusaenderung = time();
        $this->statusaenderung_userName = $userName;
    }

    public function setBemerkung(string $bem): void
    {
        $this->bemerkung = $bem;
    }

    /**
     * setzt die inhaltlichen Daten zum Antrag
     *
     * @param formData $daten
     */
    public function setDaten(formData $daten): void
    {
        $this->daten = $daten;
    }

    public function getDaten(): formData
    {
        return $this->daten;
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

    /**
     * @throws LogicException wenn Status nicht self::STATUS_AUFEGNOMMEN ist
     */
    public function getActivationUrl(): string
    {
        if ($this->getStatus() !== self::STATUS_AUFGENOMMEN) {
            throw new \LogicException('status muss STATUS_AUFGENOMMEN sein');
        }
        $token = Token::encode([$this->getId()], '', getenv('TOKEN_KEY'));
        return 'https://mitglieder.' . getenv('DOMAINNAME') . '/aufnahme?token=' . $token;
    }

    /**
     * @throws LogicException wenn Status nicht self::STATUS_AUF_ANTWORT_WARTEN ist
     */
    public function getEditUrl(): string
    {
        if ($this->getStatus() !== self::STATUS_AUF_ANTWORT_WARTEN) {
            throw new \LogicException('status muss STATUS_AUF_ANTWORT_WARTEN sein');
        }
        $token = Token::encode(['edit'], $this->getId() . $this->getTsStatusaenderung(), getenv('TOKEN_KEY'));
        return 'https://aufnahme.' . getenv('DOMAINNAME') . '/edit/' . $this->getId() . '/?token=' . $token;
    }

    public function validateEditToken(string $token): bool
    {
        try {
            return Token::decode($token, fn() => $this->getId() . $this->getTsStatusaenderung(), getenv('TOKEN_KEY')) === ['edit'];
        } catch (\Exception) {
            return false;
        }
    }

    public function getUrl(): string
    {
        return 'https://aufnahme.' . getenv('DOMAINNAME') . '/antraege/' . $this->getId() . '/';
    }

    /**
     * Get the value of ts_antrag
     *
     * @return int
     */
    public function getTsAntrag(): int
    {
        return $this->ts_antrag;
    }

    /**
     * Set the value of ts_antrag
     *
     * @param int $ts_antrag
     *
     * @return self
     */
    public function setTsAntrag(int $ts_antrag): self
    {
        $this->ts_antrag = $ts_antrag;

        return $this;
    }

    /**
     * Get the value of ts_nachfrage
     *
     * @return int
     */
    public function getTsNachfrage(): int
    {
        return $this->ts_nachfrage;
    }

    /**
     * Set the value of ts_nachfrage
     *
     * @param int $ts_nachfrage
     *
     * @return self
     */
    public function setTsNachfrage(int $ts_nachfrage): self
    {
        $this->ts_nachfrage = $ts_nachfrage;

        return $this;
    }

    /**
     * Get the value of ts_antwort
     *
     * @return int
     */
    public function getTsAntwort(): int
    {
        return $this->ts_antwort;
    }

    /**
     * Set the value of ts_antwort
     *
     * @param int $ts_antwort
     *
     * @return self
     */
    public function setTsAntwort(int $ts_antwort): self
    {
        $this->ts_antwort = $ts_antwort;

        return $this;
    }

    /**
     * Get the value of ts_entscheidung
     *
     * @return int
     */
    public function getTsEntscheidung(): int
    {
        return $this->ts_entscheidung;
    }

    /**
     * Set the value of ts_entscheidung
     *
     * @param int $ts_entscheidung
     *
     * @return self
     */
    public function setTsEntscheidung(int $ts_entscheidung): self
    {
        $this->ts_entscheidung = $ts_entscheidung;

        return $this;
    }

    /**
     * Get the value of ts_erinnerung
     *
     * @return int
     */
    public function getTsErinnerung(): int
    {
        return $this->ts_erinnerung;
    }

    /**
     * Set the value of ts_erinnerung
     *
     * @param int $ts_erinnerung
     *
     * @return self
     */
    public function setTsErinnerung(int $ts_erinnerung): self
    {
        $this->ts_erinnerung = $ts_erinnerung;

        return $this;
    }

    /**
     * Get the value of ts_statusaenderung
     *
     * @return int
     */
    public function getTsStatusaenderung(): int
    {
        return $this->ts_statusaenderung;
    }

    /**
     * Set the value of ts_statusaenderung
     *
     * @param int $ts_statusaenderung
     *
     * @return self
     */
    public function setTsStatusaenderung(int $ts_statusaenderung): self
    {
        $this->ts_statusaenderung = $ts_statusaenderung;

        return $this;
    }
}

<?php
namespace MHN\Aufnahme\Domain\Model;

/**
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

use DateTime;
use MHN\Aufnahme\Domain\Repository\UserRepository;

/**
 * Repräsentiert ein Votum auf einen Antrag
 */
class Vote
{
    /** @var int */
    const NEIN = 0;

    /** @var int */
    const JA = 1;

    /** @var int */
    const NACHFRAGEN = 2;

    /** @var int */
    const ENTHALTUNG = 3;

    /** @var string[] */
    const VALUE_READABLE = [
        self::NEIN => 'N',
        self::JA => 'J',
        self::NACHFRAGEN => '?',
        self::ENTHALTUNG => '-',
    ];

    /** @var string[] */
    const VALUE_COLORS = [
        self::NEIN => 'antrag_bewertung_rot',
        self::JA => 'antrag_bewertung_gruen',
        self::NACHFRAGEN => 'antrag_bewertung_gelb',
        self::ENTHALTUNG => 'antrag_bewertung_weiss',
    ];

    /** @var int[] */
    const VALID_VALUES = [self::NEIN, self::JA, self::NACHFRAGEN, self::ENTHALTUNG];

    /** @var int */
    private $antragId = 0;

    /** @var int */
    private $userId = 0;

    /** @var int */
    private $value = 0;

    /** @var DateTime */
    private $time = null;

    /** @var string */
    private $bemerkung = '';

    /** @var int */
    private $nachfrage = '';

    /** @var UserRepository */
    private $userRepository = null;

    /**
     * Instanziiert das Objekt
     */
    public function __construct()
    {
        $this->setTime(new DateTime());
    }

    /**
     * @return UserRepository
     */
    private function getUserRepository()
    {
        $this->userRepository = $this->userRepository ?: UserRepository::getInstance();

        return $this->userRepository;
    }

    /**
     * Gibt die ID des Antrags zurück, zu der das Votum gehört
     *
     * @return int
     */
    public function getAntragId()
    {
        return $this->antragId;
    }

    /**
     * Setzt die ID des Antrags, zu der das Votum gehört
     *
     * @param int $antragId
     *
     * @return void
     */
    public function setAntragId($antragId)
    {
        $this->antragId = $antragId;
    }

    /**
     * Gibt die User-ID des abstimmenden Users zurück.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Setzt die User-ID des abstimmenden Users.
     *
     * @param int $userId
     *
     * @return void
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Gibt den abstimmenden User zurück.
     *
     * @return User|null User-Objekt, falls gefunden
     */
    public function getUser()
    {
        return $this->getUserRepository()->findOneById($this->getUserId());
    }

    /**
     * Gibt den Namen des abstimmenden Users zurück.
     *
     * @return string
     */
    public function getUserName()
    {
        $user = $this->getUser();
        return ($user !== null) ? $user->getUserName() : 'unbekannt';
    }

    /**
     * Gibt den Zeitpunkt zurück, an dem das Votum abgegeben wurde.
     *
     * @return DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Setzt den Zeitpunkt zurück, an dem das Votum abgegeben wurde.
     *
     * @param DateTime $time
     *
     * @return void
     */
    public function setTime(DateTime $time)
    {
        $this->time = $time;
    }

    /**
     * Gibt das Abstimmungsverhalten (Vote::NEIN/JA/NACHFRAGEN/ENTHALTUNG) zurück.
     *
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Setzt das Abstimmungsverhalten (Vote::NEIN/JA/NACHFRAGEN/ENTHALTUNG).
     *
     * @param int $value
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Gibt das Abstimmungsverhalten als lesbare Kurzfassung zurück.
     *
     * @return string
     */
    public function getValueReadable()
    {
        return self::VALUE_READABLE[$this->value];
    }

    /**
     * Gibt die CSS-Klasse zur Einfärbung zurück
     *
     * @return string
     */
    public function getValueColor()
    {
        return self::VALUE_COLORS[$this->value];
    }

    /**
     * Gibt die Bemerkung zurück.
     *
     * @return string
     */
    public function getBemerkung()
    {
        return $this->bemerkung;
    }

    /**
     * Setzt die Bemerkung.
     *
     * @param string $bemerkung
     *
     * @return void
     */
    public function setBemerkung($bemerkung)
    {
        $this->bemerkung = $bemerkung;
    }

    /**
     * Gibt den Nachfragen-Text zurück.
     *
     * @return string
     */
    public function getNachfrage()
    {
        return $this->nachfrage;
    }

    /**
     * Setzt den Nachfragen-Text.
     *
     * @param string $nachfrage
     *
     * @return void
     */
    public function setNachfrage($nachfrage)
    {
        $this->nachfrage = $nachfrage;
    }
}

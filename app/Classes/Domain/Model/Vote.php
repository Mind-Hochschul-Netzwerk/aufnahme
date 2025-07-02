<?php
namespace App\Domain\Model;

/**
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

use DateTime;
use App\Domain\Model\User;
use App\Domain\Repository\UserRepository;

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

    /** @var string */
    private $userName = '';

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
    private function getUserRepository(): UserRepository
    {
        $this->userRepository = $this->userRepository ?: UserRepository::getInstance();

        return $this->userRepository;
    }

    /**
     * Gibt die ID des Antrags zurück, zu der das Votum gehört
     */
    public function getAntragId(): int
    {
        return $this->antragId;
    }

    /**
     * Setzt die ID des Antrags, zu der das Votum gehört
     */
    public function setAntragId(int $antragId): void
    {
        $this->antragId = $antragId;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    /**
     * Gibt den abstimmenden User zurück.
     *
     * @return User|null User-Objekt, falls gefunden
     */
    public function getUser(): ?User
    {
        return $this->getUserRepository()->findOneByUserName($this->getUserName());
    }

    public function getRealName(): string
    {
        return $this->getUser()->getRealName();
    }

    /**
     * Gibt den Zeitpunkt zurück, an dem das Votum abgegeben wurde.
     *
     * @return DateTime
     */
    public function getTime(): DateTime
    {
        return $this->time;
    }

    /**
     * Setzt den Zeitpunkt zurück, an dem das Votum abgegeben wurde.
     */
    public function setTime(DateTime $time): void
    {
        $this->time = $time;
    }

    /**
     * Gibt das Abstimmungsverhalten (Vote::NEIN/JA/NACHFRAGEN/ENTHALTUNG) zurück.
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * Setzt das Abstimmungsverhalten (Vote::NEIN/JA/NACHFRAGEN/ENTHALTUNG).
     */
    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    /**
     * Gibt das Abstimmungsverhalten als lesbare Kurzfassung zurück.
     */
    public function getValueReadable(): string
    {
        return self::VALUE_READABLE[$this->value];
    }

    /**
     * Gibt die CSS-Klasse zur Einfärbung zurück
     */
    public function getValueColor(): string
    {
        return self::VALUE_COLORS[$this->value];
    }

    /**
     * Gibt die Bemerkung zurück.
     */
    public function getBemerkung(): string
    {
        return $this->bemerkung;
    }

    /**
     * Setzt die Bemerkung.
     */
    public function setBemerkung(string $bemerkung): void
    {
        $this->bemerkung = $bemerkung;
    }

    /**
     * Gibt den Nachfragen-Text zurück.
     */
    public function getNachfrage(): string
    {
        return $this->nachfrage;
    }

    /**
     * Setzt den Nachfragen-Text.
     */
    public function setNachfrage(string $nachfrage): void
    {
        $this->nachfrage = $nachfrage;
    }
}

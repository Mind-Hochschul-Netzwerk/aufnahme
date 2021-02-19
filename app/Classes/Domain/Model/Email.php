<?php
namespace MHN\Aufnahme\Domain\Model;

/**
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

use DateTime;

/**
 * Repräsentiert eine archivierte E-Mail
 */
class Email
{
    /** @var int */
    private $antragId = 0;

    /** @var string */
    private $grund = '';

    /** @var int */
    private $senderUserId = 0;

    /** @var DateTime */
    private $creationTime = null;

    /** @var string */
    private $subject = '';

    /** @var string */
    private $text = '';

    /**
     * Instanziiert das Objekt
     */
    public function __construct()
    {
        $this->setCreationTime(new DateTime());
    }

    /**
     * Gibt den Antrag-ID zurück.
     *
     * @return int
     */
    public function getAntragId()
    {
        return $this->antragId;
    }

    /**
     * Setzt die Antrag-ID
     *
     * @param int $antragId
     * @return void
     */
    public function setAntragId($antragId)
    {
        $this->antragId = $antragId;
    }

    /**
     * Gibt den Grund für die E-Mail zurück
     *
     * @return string
     */
    public function getGrund()
    {
        return $this->grund;
    }

    /**
     * Setzt den Grund für die E-Mail.
     *
     * @param string $grund
     * @return void
     */
    public function setGrund($grund)
    {
        $this->grund = $grund;
    }

    /**
     * Gibt die User-ID des Absenders zurück.
     *
     * @return int
     */
    public function getSenderUserId()
    {
        return $this->senderUserId;
    }

    /**
     * Setzt die User-ID des Absenders.
     *
     * @param int $senderUserId
     * @return void
     */
    public function setSenderUserId($senderUserId)
    {
        $this->senderUserId = $senderUserId;
    }

    /**
     * Gibt den Erstellungszeitpunkt der E-Mail zurück.
     *
     * @return DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * Setzt den Erstellungszeitpunkt der E-Mail.
     *
     * @param DateTime $creationTime
     * @return void
     */
    public function setCreationTime(DateTime $creationTime)
    {
        $this->creationTime = $creationTime;
    }

    /**
     * Gibt den Betreff zurück.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Setzt den Betreff.
     *
     * @param string $subject
     * @return void
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Gibt den Textinhalt zurück.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Setzt den Textinhalt.
     *
     * @param string $text
     * @return void
     */
    public function setText($text)
    {
        $this->text = $text;
    }
}

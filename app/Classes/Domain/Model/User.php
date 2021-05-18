<?php
namespace MHN\Aufnahme\Domain\Model;

/**
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 */

/**
 * Repräsentiert einen Benutzer
 */
class User
{
    /** @var string */
    private $userName = '';

    /** @var string */
    private $realName = '';

    /** @var bool */
    private $hasRole = true;

    /** @var string */
    private $emailAddress = '';

    public function __construct(string $userName, string $realName, string $emailAddress, bool $hasAufnahmeRole)
    {
        $this->userName = $userName;
        $this->realName = $realName;
        $this->emailAddress = $emailAddress;
        $this->hasRole = $hasAufnahmeRole;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getRealName(): string
    {
        return $this->realName;
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public function hasAufnahmeRole(): bool
    {
        return $this->hasRole;
    }
}

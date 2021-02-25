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

    public function __construct(string $userName, string $realName, bool $hasAufnahmeRole)
    {
        $this->userName = $userName;
        $this->realName = $realName;
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

    public function hasAufnahmeRole(): bool
    {
        return $this->hasRole;
    }
}

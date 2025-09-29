<?php
/**
 * @author Henrik Gebauer <henrik@mind-hochschul-netzwerk.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

declare(strict_types=1);

namespace App\Model;

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

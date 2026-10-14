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

    public function __construct(string $userName, string $realName)
    {
        $this->userName = $userName;
        $this->realName = $realName;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getRealName(): string
    {
        return $this->realName;
    }
}

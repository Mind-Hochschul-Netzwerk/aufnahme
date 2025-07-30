<?php
declare(strict_types=1);
namespace App\Service;

use App\Interfaces\Singleton;
use App\Model\User;
use App\Repository\UserRepository;
use Hengeb\Router\Exception\NotLoggedInException;
use Hengeb\Router\Interface\CurrentUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

/**
 * represents the Current User
 */
class CurrentUser implements Singleton, CurrentUserInterface {
    use \App\Traits\Singleton;

    private ?Request $request = null;

    private ?User $user = null;

    public function setRequest(Request $request): void
    {
        $this->request = $request;

        if (!$request->hasSession()) {
            $request->setSession(new Session());
        }

        $userName = $request->getSession()->get('userName');
        $this->user = $userName ? UserRepository::getInstance()->findOneByUserName($userName) : null;
    }

    private function assertLogin(): void
    {
        if (!$this->user) {
            throw new NotLoggedInException();
        }
    }

    public function __call($method, $arguments)
    {
        $this->assertLogin();
        return call_user_func_array([$this->user, $method], $arguments);
    }

    public function __get($property)
    {
        if (!$this->user) {
            return null;
        }
        return $this->user->$property;
    }

    public function __set($property, $value)
    {
        $this->assertLogin();
        $this->user->$property = $value;
    }

    public function isLoggedIn(): bool
    {
        return boolval($this->user);
    }

    public function isBackendConnection(): bool
    {
        $ip = ip2long($_SERVER['REMOTE_ADDR']);
        return ((ip2long("172.16.0.0") <= $ip && $ip <= ip2long("172.31.255.255"))
            || (ip2long("192.168.0.0") <= $ip && $ip <= ip2long("192.168.255.255")));
    }


    public function logIn(User $user)
    {
        if (!$this->request) {
            throw new \LogicException('request is not set', 1729975906);
        }
        $this->request->getSession()->set('userName', $user->getUserName());
        $this->user = $user;
    }

    public function logOut(): void
    {
        if (!$this->request) {
            throw new \LogicException('request is not set', 1729975906);
        }
        $this->request->getSession()->remove('userName');
        $this->user = null;
    }

    public function getWrappedUser(): ?User
    {
        return $this->user;
    }
}

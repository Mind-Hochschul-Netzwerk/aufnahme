<?php
namespace MHN\Aufnahme\Service;

/**
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

use MHN\Aufnahme\Domain\Model\User;
use MHN\Aufnahme\Domain\Repository\UserRepository;
use Smarty;

/**
 * Verwaltet die Login-Session und schützt interne Seiten
 */
class LoginGatekeeper implements \MHN\Aufnahme\Interfaces\Singleton
{
    use \MHN\Aufnahme\Traits\Singleton;

    /** @var int seconds */
    const TIMEOUT_IN_SECONDS = 60 * 60;

    /** @var User */
    private $user = null;

    /** @var Smarty */
    private $smarty = null;

    /**
     * Initialisierung
     *
     * @return void
     */
    private function __construct()
    {
        $this->smarty = SmartyContainer::getInstance()->getSmarty();
    }

    /**
     * Aktualisiert den Login-Status abhängig von der Session und Formulardaten.
     *
     * @return void
     */
    public function updateLoginStatus()
    {
        $this->updateLoggedInUserBySession();

        $this->tryLoginWithRequest();
        $this->tryLogoutWithRequest();

        $this->checkForSessionTimeout();
    }

    /**
     * Gibt den eingeloggten Benutzer zurück oder null, falls der Benutzer nicht
     * eingeloggt ist.
     *
     * @return User|null
     */
    public function getLoggedInUser()
    {
        return $this->user;
    }

    /**
     * Loggt den User ein.
     *
     * @param User $user
     * @return void
     */
    public function logIn(User $user)
    {
        $this->user = $user;
        // neue Session-ID zuweisen, um Session-Hijacking-Gefahr zu minimieren
        Session::getInstance()->regenerateId();
        $_SESSION['userName'] = $this->user->getUserName();
        $this->updateLoggedInUserBySession();
    }

    /**
     * Loggt den laut Session eingeloggten User aus.
     *
     * @return void
     */
    public function logOut()
    {
        unset($_SESSION['userName']);
        $this->updateLoggedInUserBySession();
    }

    /**
     * Prüft, ob der User eingeloggt ist.
     *
     * @return bool
     */
    public function isUserLoggedIn()
    {
        return $this->user !== null;
    }

    /**
     * Prüft, ob der angegebene Benutzer der ist, der gerade laut Session
     * eingeloggt ist.
     *
     * @param User $user
     * @return bool
     */
    public function hasCurrentLoginSession(User $user)
    {
        return $this->isUserLoggedIn() && $this->getLoggedInUser()->getUserName() === $user->getUserName();
    }

    /**
     * Zeigt das Login-Formular an und bricht die weitere Ausführung ab.
     *
     * @param text $message Meldung, die ggf. angezeigt wird.
     * @return void
     */
    private function exitToLoginForm($message = '')
    {
        $this->smarty->assign('message', $message);
        $this->smarty->assign('innentemplate', 'bitteeinloggen.tpl');
        $this->smarty->display('main.tpl');
        exit;
    }

    /**
     * Prüft, ob der User sich einloggen möchte, und versucht ggf. dies umzusetzen
     *
     * @return void
     */
    private function tryLoginWithRequest()
    {
        if (!isset($_POST['login']) || !isset($_POST['password'])) {
            return;
        }

        $user = UserRepository::getInstance()->findOneByCredentials($_POST['login'], $_POST['password']);
        if ($user === null) {
            $this->exitToLoginForm('Login fehlgeschlagen');
            return;
        }

        $this->login($user);
    }

    /**
     * Prüft, ob der User sich ausloggen möchte und loggt ihn ggf. aus.
     *
     * @return void
     */
    private function tryLogoutWithRequest()
    {
        if (!isset($_POST['ausloggen']) || $_POST['ausloggen'] !== 'ja') {
            return;
        }

        $this->logOut();
        $this->exitToLoginForm('Abmeldevorgang erfolgreich.');
    }

    /**
     * Prüft, ob die Session abgelaufen ist, und loggt den User ggf. aus.
     * Gesendete Formulardaten werden gespeichert.
     *
     * Aktualisiert außerdem die Zeit der letzten Aktivität.
     *
     * @return void
     */
    private function checkForSessionTimeout()
    {
        if (!$this->isUserLoggedIn() || Session::getInstance()->getInactivityTimeInSeconds() < self::TIMEOUT_IN_SECONDS) {
            return;
        }

        $this->logOut();
        $message = 'Aufgrund langer Inaktivität wurdest du automatisch ausgeloggt. ';
        if (count($_POST) > 0) {
            $message .= 'Die Daten, die du eben abgeschickt hast, sind jedoch nicht verloren, ' .
                'wenn du dich gleich wieder mit deinem Benutzernamen und deinem Passwort anmeldest.';
        }
        $this->smarty->assign('login_posts', $_POST);
        $this->exitToLoginForm($message);
    }

    /**
     * Lädt und aktualisiert das in der Session referenzierte Benutzerobjekt aus der Datenbank.
     *
     * return @void
     */
    public function updateLoggedInUserBySession()
    {
        $this->smarty->assign('entry_username', null);
        $this->smarty->assign('entry_angemeldet', false);
        $this->user = null;

        
        if (isset($_SESSION['userName'])) {
            $this->user = UserRepository::getInstance()->findOneByUserName($_SESSION['userName']);
            if ($this->user === null || !$this->user->hasAufnahmeRole()) {
                $this->logOut();
                $this->exitToLoginForm('Du hast kein Zugriffsrecht mehr.');
            }
            $this->smarty->assign('entry_username', $this->user->getUserName());
            $this->smarty->assign('entry_angemeldet', true);
        }
    }

    /**
     * Schützt die Seite vor Zugriffen von nicht eingeloggten Usern.
     * Falls der User nicht eingeloggt ist, wird ein Loginformular angezeigt.
     *
     * @return void
     */
    public function protectThisPage()
    {
        if (!$this->isUserLoggedIn()) {
            $this->exitToLoginForm();
        }
    }
}

<?php
namespace MHN\Aufnahme;

use MHN\Aufnahme\Domain\Model\User;
use MHN\Aufnahme\Domain\Repository\UserRepository;
use Smarty;

class UserController
{
    /** @var Smarty */
    private $smarty = null;

    public $parameter;

    /** @var UserRepository */
    private $userRepository;

    public function __construct($parameter)
    {
        $this->parameter =& $parameter;
        $this->smarty = Service\SmartyContainer::getInstance()->getSmarty();
        $this->userRepository = UserRepository::getInstance();
    }

    /**
     * Prüft, ob ein Username vergeben werden kann, d.h. ob er das korrekte Format hat
     * und nicht schon an einen anderen Benutzer vergeben ist.
     *
     * @param User $user
     * @param string $userName neuer Name
     * @return bool Name erlaubt
     */
    private function checkUserName(User $user, $userName)
    {
        if (!User::isUsernameValid($userName)) {
            return false;
        }

        $userByName = $this->userRepository->findOneByName($userName);

        return $userByName === null || $userByName->getId() === $user->getId();
    }

    /**
     * Speichert einen neuen Benutzer.
     *
     * @return void
     */
    public function speichern_uebersicht()
    {
        //Formulare auf der Übersichts-Seite verarbeiten
        if (!isset($_POST['formular']) or $_POST['formular'] != 'neu') {
            return;
        }

        if (!isset($_POST['passwort']) || !isset($_POST['passwort2']) || !isset($_POST['name'])
            || !isset($_POST['realname'])
        ) {
            // keine hübsche Fehlermeldung, da nur durch einen Angriff möglich
            die('POST-Daten unvollständig');
        }

        if ($_POST['passwort'] != $_POST['passwort2']) {
            $this->smarty->assign('meldung', 'Fehler: Passwörter stimmen nicht überein');
            return;
        }

        $user = new User();

        if ($this->checkUserName($user, $_POST['name']) === false) {
            $this->smarty->assign(
                'meldung',
                'Der Benutzername hat ein ungültiges Format (zu kurz oder ungültige Zeichen) oder ist bereits vergeben.'
            );
            return;
        }

        $user->setUsername($_POST['name']);
        $user->setPassword($_POST['passwort']);
        $user->setRealName($_POST['realname']);

        $this->userRepository->save($user);

        $this->smarty->assign('meldung', 'Der Benutzer wurde erfolgreich hinzugefügt');
    }

    /**
     * Speichert Änderung zu einem Benutzer
     *
     * @param int $id
     * @return bool false, falls der Benutzer gelöscht wurde.
     */
    private function speichern_webuser($id)
    {
        if (!isset($_POST['formular'])) {
            return true;
        }
        if ($_POST['formular'] != 'webuser' and $_POST['formular'] != 'loeschen') {
            return true;
        }
        $user = $this->userRepository->findOneById($id);
        if ($user === null) {
            die('interner Fehler: Benutzer nicht gefunden');
        }
        if ($_POST['formular'] == 'webuser') {
            if (!isset($_POST['passwort']) || !isset($_POST['passwort2']) || !isset($_POST['name'])
                || !isset($_POST['realname'])
            ) {
                // keine hübsche Fehlermeldung, da nur durch einen Angriff möglich
                die('POST-Daten unvollständig');
            }

            if ($this->checkUserName($user, $_POST['name']) === false) {
                $this->smarty->assign(
                    'meldung',
                    'Der Benutzername hat ein ungültiges Format (zu kurz oder ungültige Zeichen) ' .
                    'oder ist bereits vergeben.'
                );
                return true;
            }

            if (($_POST['passwort'] !== '' || $_POST['passwort2'] !== '')) {
                if ($_POST['passwort'] !== $_POST['passwort2']) {
                    $this->smarty->assign('meldung', 'Fehler: Passwörter stimmen nicht überein');
                    return true;
                }
                $user->setPassword($_POST['passwort']);
            }

            $user->setUserName($_POST['name']);
            $user->setRealName($_POST['realname']);

            //alle ok, speichern:
            $this->userRepository->save($user);

            // falls der eigene Benutzername geändert wurde:
            Service\LoginGatekeeper::getInstance()->updateLoggedInUserBySession();

            $this->smarty->assign('meldung', 'Änderungen gespeichert');
            return true;
        } elseif ($_POST['formular'] == 'loeschen') {
            if (!isset($_POST['wirklich']) or $_POST['wirklich'] != 'ja') {
                $this->smarty->assign('meldung', 'Nicht gelöscht, da "wirklich" nicht angekreuzt war');
                return true;
            }
            if (Service\LoginGatekeeper::getInstance()->hasCurrentLoginSession($user)) {
                $this->smarty->assign('meldung', 'Du kannst nicht deinen eigenen Zugang löschen.');
                return true;
            }
            $this->userRepository->delete($user);
            $this->smarty->assign('meldung', 'Benutzer erfolgreich gelöscht');
            return false;
        }
    }

    public function exec()
    {
        $id = $this->parameter['urlparams'];
        $id = (int)trim($id, '/');
        if ($id === 0) {
            //speichern:
            $this->speichern_uebersicht();
            //Übersicht laden
            $this->smarty->assign('modus', 'uebersicht');
            $users = UserRepository::getInstance()->findAll();
            $this->smarty->assign('webusers', $users);
        } else {
            //speichern:
            $res = $this->speichern_webuser($id);
            $this->smarty->assign('modus', 'webuser');
            if ($res === false) {
                //Der Benutzer wurde gerade gelöscht; leeren Benutzer zurückgeben:
                $this->smarty->assign('webuser', new User());
                return;
            }

            //einzelnen Benutzer laden
            $user = $this->userRepository->findOneById($id);
            $this->smarty->assign(
                'loeschen_erlaubt',
                !Service\LoginGatekeeper::getInstance()->hasCurrentLoginSession($user)
            );
            if ($user === null) {
                $this->smarty->assign('modus', 'nichtgefunden');
                return;
            }
            $this->smarty->assign('webuser', $user);
        }
    }
}

function benutzer__laden($parameter)
{
    $b = new UserController($parameter);
    $b->exec();
}

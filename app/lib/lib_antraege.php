<?php
namespace MHN\Aufnahme;

use MHN\Aufnahme\Domain\Model\Email;
use MHN\Aufnahme\Domain\Model\Vote;
use MHN\Aufnahme\Domain\Repository\EmailRepository;
use MHN\Aufnahme\Domain\Repository\TemplateRepository;
use MHN\Aufnahme\Domain\Repository\UserRepository;
use MHN\Aufnahme\Domain\Repository\VoteRepository;
use PHPMailer;
use Smarty;
use MHN\Aufnahme\Antrag;
use MHN\Aufnahme\Service\Token;
use MHN\Aufnahme\Service\EmailService;

class lib_antraege
{
    public $parameter;

    /** @var Smarty */
    private $smarty = null;

    /** @var User */
    private $loggedInUser = null;

    /** @var Antrag */
    private $antrag = null;

    //konstruktor:
    public function __construct($parameter)
    {
        if (!isset($_POST['formular'])) {
            $_POST['formular'] = null;
        }
        $this->parameter = $parameter;
        $this->smarty = Service\SmartyContainer::getInstance()->getSmarty();
        $this->loggedInUser = Service\LoginGatekeeper::getInstance()->getLoggedInUser();
    }

    //abschnitt 1: Antrags-Grunddaten
    public function speichern_antrag1()
    {
        global $global_status;
        if ($_POST['formular'] != 'speichern_antrag1') {
            return;
        }

        if (!isset($_POST['status'])) {
            die('Statuscode in $_POST[status] nicht gesetzt');
        }
        if (!isset($global_status[$_POST['status']])) {
            die('Ungültiger Statuscode');
        }

        $this->antrag->setStatus((int)$_POST['status'], $this->loggedInUser->getUserName());

        $this->antrag->setBemerkung($_POST['bemerkung']);
        $ts_nachfrage = Util::datumToTs($_POST['datum_nachfrage']);
        $ts_antwort = Util::datumToTs($_POST['datum_antwort']);
        $ts_entscheidung = Util::datumToTs($_POST['datum_entscheidung']);
        if ($ts_nachfrage === false || $ts_antwort === false || $ts_entscheidung === false) {
            $this->smarty->assign('meldung', 'Fehler im Datum-Format.');
            return;
        }
        $this->antrag->setTsNachfrage($ts_nachfrage);
        $this->antrag->setTsAntwort($ts_antwort);
        $this->antrag->setTsEntscheidung($ts_entscheidung);
        if ($this->antrag->save()) {
            $this->smarty->assign('meldung', 'Neuen Status gespeichert.');
        } else {
            $this->smarty->assign('meldung', 'Konnte Status nicht speichern.');
        }
    }

    public function speichern_antrag_daten()
    {
        if ($_POST['formular'] != 'speichern_antrag_daten') {
            return;
        }
        $daten = Daten::datenByAntragId($this->antrag->getId());

        // leere Checkboxen werden nicht gesendet
        foreach (Daten::KEYS_CHECKBOX as $key) {
            $_POST[$key] = isset($_POST[$key]) ? 'j' : 'n';
        }

        foreach (Daten::KEYS as $key) {
            // nicht im Formular änderbar:
            if (in_array($key, [
                'user_email',
                'kenntnisnahme_datenverarbeitung',
                'kenntnisnahme_datenverarbeitung_text',
                'einwilligung_datenverarbeitung',
                'einwilligung_datenverarbeitung_text'
            ], true)) {
                continue;
            }
            if (!property_exists($daten, $key)) {
                die('Unbekannte Property in daten: ' . $key);
            }
            if (!isset($_POST[$key])) {
                die('nicht vom Formular gesetzt: ' . $key);
            }
            if ($key === 'mhn_geburtstag') {
                $daten->$key = Daten::parseBirthdayInput($_POST[$key]) ?? '0000-00-00';
                continue;
            }
            $daten->$key = $_POST[$key];
        }
        $daten->save();
        //Fragen muessen extra gespeichert werden, und zwar im Antrag:
        global $global_fragen;
        $fragenWerte = $this->antrag->getFragenWerte();
        foreach ($global_fragen as $key => $frage) {
            assert(array_key_exists($key, $fragenWerte));
            assert(array_key_exists($key, $_POST));
            $fragenWerte[$key] = $_POST[$key];
        }
        $this->antrag->setFragenWerte($fragenWerte);
        $this->antrag->save();
    }

    /**
     * Abschnitt 2: Voten speichern
     *
     * Falls ein Votum abgegeben wurde, wird die Auführung beendet und zur Übersicht weitergeleitet.
     *
     * @return void
     */
    public function speichern_antrag_votes()
    {
        if ($_POST['formular'] != 'speichern_antrag_voten') {
            return;
        }
        if (!in_array((int)$_POST['votum'], Vote::VALID_VALUES, true)) {
            // nur durch einen Angriff möglich
            die('ungültiger Wert für votum');
        }

        $vote = new Vote();
        $vote->setUserName($this->loggedInUser->getUserName());
        $vote->setAntragId($this->antrag->getId());
        $vote->setValue((int)$_POST['votum']);
        $vote->setNachfrage(trim($_POST['nachfrage']));
        $vote->setBemerkung(trim($_POST['bemerkung']));

        if ($vote->getValue() === Vote::NACHFRAGEN && $vote->getNachfrage() === '') {
            // nachfragen, aber dort nichts eingetragen
            $this->smarty->assign('meldung', 'Fehler: "Nachfragen" gevotet, aber dort nichts eingetragen');
            $this->smarty->assign('bemerkung', $_POST['bemerkung']);
            $this->smarty->assign('nachfrage', $_POST['nachfrage']);
            return;
        }
        if ($vote->getValue() === Vote::JA && $vote->getNachfrage() !== '') {
            // Ja, aber in Nachfragen etwas eingetragen
            $this->smarty->assign('meldung', 'Fehler: "Ja" gevotet, aber in "Nachfragen" etwas eingetragen');
            $this->smarty->assign('bemerkung', $_POST['bemerkung']);
            $this->smarty->assign('nachfrage', $_POST['nachfrage']);
            return;
        }

        VoteRepository::getInstance()->add($vote);

        header('Location: /antraege/');
    }

    public function speichern_antrag_kommentare()
    {
        if ($_POST['formular'] === 'speichern_antrag_kommentare' && !empty($_POST['k_add'])) {
            $kommentar = $_POST['kommentar'];
            $kommentar = trim($kommentar);
            if ($kommentar == '') {
                $this->smarty->assign('meldung', 'Kommentar war leer. Daher nicht hinzugefügt.');
                return;
            }
            $this->antrag->addKommentar($this->loggedInUser->getUserName(), $_POST['kommentar']);
            if ($this->antrag->save()) {
                $this->smarty->assign('meldung', 'Kommentar hinzugefügt');
            } else {
                $this->smarty->assign('meldung', 'Fehler beim Hinzufügen des Kommentars');
            }
        } elseif ($_POST['formular'] === 'kommentare_editieren') {
            $this->antrag->setKommentare($_POST['kommentare']);
            if ($this->antrag->save()) {
                $this->smarty->assign('meldung', 'Kommentare geändert');
            } else {
                $this->smarty->assign('meldung', 'Fehler beim Ändern von Kommentaren');
            }
        }
    }

    public function exec()
    {
        //Antrag-ID aus der URL:
        preg_match("/^(\d+)\/(\w*)/", $this->parameter['urlparams'], $matches);

        $id = isset($matches[1]) ? (int)$matches[1] : 0;
        $aktion = isset($matches[2]) ? $matches[2] : null;

        if ($aktion && !in_array($aktion, ['aufnehmen', 'nachfragen', 'ablehnen'])) {
            die("invalid request");
        }

        // keine ID angegeben => Übersicht der Anträge anzeigen
        if ($id === 0) {
            // Übersicht laden
            if (!empty($this->parameter['archiv'])) {
                $this->smarty->assign('innentemplate', 'antraege/archiv.tpl');
                $antraege = Antrag::alleEntschiedenenAntraege();
            } else {
                $this->smarty->assign('innentemplate', 'antraege/uebersicht.tpl');
                $antraege = Antrag::alleOffenenAntraege();
            }
            // ggf. alle rausschmeissen, die selbst schon gevotet:
            if (strpos($this->parameter['urlparams'], 'nichtvonmirgevotet') !== false) {
                foreach ($antraege as $k => $antrag) {
                    $vote = $antrag->getLatestVoteByUserName($this->loggedInUser->getUserName());

                    if ($vote === null) {
                        continue;
                    }

                    // wenn Antragsstatus "neu bewerten" und seitdem nicht neu bewertet: auch behalten!
                    if ($antrag->getStatus() == Antrag::STATUS_NEU_BEWERTEN && $votum->getTime()->getTimestamp() < $antrag->ts_statusaenderung) {
                        continue;
                    }

                    unset($antraege[$k]);
                }
                $this->smarty->assign('nichtvonmirgevotet', true);
            }
            // die Voten extrahieren:
            $userNames_gevotet = [];
            foreach ($antraege as $antrag) {
                $votes = $antrag->getVotes();
                foreach ($votes as $vote) {
                    $userName = $vote->getUserName();
                    if (!in_array($userName, $userNames_gevotet, true)) {
                        $userNames_gevotet[] = $userName;
                    }
                }
            }
            $realNames = [];
            foreach ($userNames_gevotet as $userName) {
                $user = UserRepository::getInstance()->findOneByUserName($userName);
                if (!$user) {
                    $realName[$userName] = '(unbekannt)';
                } else {
                    $realNames[$userName] = $user->getRealName();
                }
            }
            $this->smarty->assign('realNames', $realNames);
            $this->smarty->assign('userNames_gevotet', $userNames_gevotet);
            $this->smarty->assign('antraege', $antraege);

            return;
        }

        try {
            $this->antrag = new Antrag($id);
        } catch (\InvalidArgumentException $e) {
            $this->smarty->assign('innentemplate', 'antraege/nicht-gefunden.tpl');
            return;
        }

        // Keine Aktion angegeben => Antrag anzeigen
        if (!$aktion) {
            //speichern:
            $this->speichern_antrag1();
            $this->speichern_antrag_votes();
            $this->speichern_antrag_kommentare();
            $this->speichern_antrag_daten();
            // nach dem Speichern: nochmal laden (damit wirklich das dargestellt ist,
            // was gespeichert wurde ...):
            $this->antrag = new Antrag($id);
            $this->smarty->assign('antrag', $this->antrag);
            $this->smarty->assign('daten', $this->antrag->daten);
            $this->smarty->assign('werte', $werte = $this->antrag->daten->toArray());
            global $global_fragen;
            $this->smarty->assign('fragen', $global_fragen);
            $this->smarty->assign('fragen_werte', $this->antrag->getFragenWerte());
            $this->smarty->assign('innentemplate', 'antraege/einzelansicht.tpl');
            if (isset($_POST['formular']) && $_POST['formular'] == 'speichern_antrag_kommentare'
                && $_POST['k_edit'] != ''
            ) {
                $this->smarty->assign('innentemplate', 'antraege/kommentare-editieren.tpl');
            }
            $this->smarty->assign('heute', Util::tsToDatum(time()));
            global $global_status;
            $this->smarty->assign('global_status', $global_status);

            $emails = EmailRepository::getInstance()->findByAntrag($this->antrag);
            $emailData = [];
            foreach ($emails as $email) {
                $user = UserRepository::getInstance()->findOneByUserName($email->getSenderUserName());
                $emailData[] = [
                    'userName' => ($user !== null) ? $user->getUserName() : 'unbekannt',
                    'time' => $email->getCreationTime()->getTimestamp(),
                    'grund' => ucfirst($email->getGrund()),
                ];
            }
            $this->smarty->assign('mails', $emailData);
            return;

        }

        // Aktion (aufnehmen, nachfragen, ablehnen) zu einem Antrag ausführen
        $this->smarty->assign('antrag', $this->antrag);
        $this->smarty->assign('heute', Util::tsToDatum(time()));
        $aktion = trim($aktion, '/');

        $this->smarty->assign('absende_email_kand', getenv('FROM_ADDRESS'));

        $this->{'aktion_' . $aktion . '_speichern'}();
        $this->{'aktion_' . $aktion . '_laden'}();
        $this->smarty->assign('innentemplate', 'antraege/aktion/' . $aktion . '.tpl');
        $this->smarty->assign('aktion', $aktion);

        $template = TemplateRepository::getInstance()->getOneByName($aktion);
        $this->smarty->assign('mailSubject', $template->getSubject());
        $replacements = [
            'vorname' => $this->antrag->getVorname(),
            'autor' => $this->loggedInUser->getRealName(),
        ];
        if ($aktion === 'nachfragen') {
            $fragen = array_filter(array_map(function ($vote) {
                return trim($vote->getNachfrage());
            }, $this->antrag->getVotes()));
            $replacements['fragen'] = implode("\n\n", $fragen);
        }
        $this->smarty->assign('mailText', $template->getFinalText($replacements));
    }

    /**
     * Sendet eine E-Mail an den Kandidaten
     *
     * @param string $betreff
     * @param string $inhalt
     * @param string $inhalt_alt Mailinhalt für's Archiv (ohn das Benutzerpasswort), falls leer, wird $inhalt verwendet.
     * @param string $grund muss 'aufnahme', 'nachfrage' oder 'ablehnung' sein
     * @return void
     */
    private function sende_email_kand(string $betreff, string $inhalt, string $grund, string $inhalt_alt = '')
    {
        $db_mail = new Email();
        $db_mail->setAntragId($this->antrag->getID());
        $db_mail->setGrund($grund);
        $db_mail->setSenderUserName($this->loggedInUser->getUserName());
        $db_mail->setSubject($betreff);
        if ($inhalt_alt == '') {
            $db_mail->setText($inhalt);
        } else {
            $db_mail->setText($inhalt_alt);
        }
        EmailService::getInstance()->send($this->antrag->getEMail(), $betreff, $inhalt);
        UserRepository::getInstance()->sendEmailToAll($betreff, $inhalt_alt ? $inhalt_alt : $inhalt);
        EmailRepository::getInstance()->add($db_mail);
    }

    /**
     * Die Aktion "aufnahme" verarbeiten
     *
     * @return void
     */
    private function aktion_aufnehmen_speichern()
    {
        if ($_POST['formular'] != 'aufnehmen') {
            return;
        }

        $mailtext_orig = $_POST['mailtext'];

        // Aktivierungslink ersetzen:
        $token = Token::encode([$this->antrag->getId()], '', getenv('TOKEN_KEY'));
        $link = 'https://mitglieder.' . getenv('DOMAINNAME') . '/aufnahme.php?token=' . $token;
        $mailtext = str_replace('{$url}', $link, $mailtext_orig);

        try {
            $this->sende_email_kand($_POST['betreff'], $mailtext, 'aufnahme', $mailtext_orig);
        } catch (\PHPMailerException $e) {
            $this->smarty->assign('meldung_speichern', 'Fehler beim E-Mail versenden der E-Mail: ' . $e->errorMessage());
            return;
        }

        $this->antrag->setStatus(Antrag::STATUS_AUFGENOMMEN, $this->loggedInUser->getUserName());
        $this->antrag->setTsEntscheidung(time());

        if (!$this->antrag->save()) {
            $this->smarty->assign('meldung_speichern', 'Fehler beim Ändern des Status. Die E-Mail wurde aber versandt.');
        } else {
            $this->smarty->assign('meldung_speichern', 'Ok: E-Mail versandt und Status geändert.');
        }
    }

    private function aktion_aufnehmen_laden()
    {
        if (in_array($this->antrag->getStatus(), [Antrag::STATUS_AUFGENOMMEN, Antrag::STATUS_ABGELEHNT]) && $_POST['formular'] != 'aufnahme') {
            $this->smarty->append('meldungen_laden',
                'Warnung: Der Antragsstatus ist bereits "' . $this->antrag->getStatusReadable() . '".'
            );
        }
    }

    /**
     * Die Aktion "nachfrage" verarbeiten
     *
     * @return void
     */
    private function aktion_nachfragen_speichern()
    {
        if ($_POST['formular'] != 'nachfragen') {
            return;
        }

        try {
            $this->sende_email_kand($_POST['betreff'], $_POST['mailtext'], 'nachfrage');
        } catch (\PHPMailerException $e) {
            $this->smarty->assign('meldung_speichern', 'Fehler beim E-Mail versenden der E-Mail: ' . $e->errorMessage());
            return;
        }

        $this->antrag->setStatus(Antrag::STATUS_AUF_ANTWORT_WARTEN, $this->loggedInUser->getUserName());
        $this->antrag->setTsNachfrage(time());
        if (!$this->antrag->save()) {
            $this->smarty->assign('meldung_speichern', 'Fehler beim Ändern des Status. Die E-Mail wurde aber versandt.');
        } else {
            $this->smarty->assign('meldung_speichern', 'Ok: E-Mail versandt und Status geändert.');
        }
    }

    private function aktion_nachfragen_laden()
    {
    }

    /**
     * Die Aktion "ablehnen" verarbeiten
     *
     * @return void
     */
    private function aktion_ablehnen_speichern()
    {
        if ($_POST['formular'] != 'ablehnen') {
            return;
        }

        try {
            $this->sende_email_kand($_POST['betreff'], $_POST['mailtext'], 'ablehnung');
        } catch (\PHPMailerException $e) {
            $this->smarty->assign('meldung_speichern', 'Fehler beim E-Mail versenden der E-Mail: ' . $e->errorMessage());
            return;
        }

        $this->antrag->setStatus(Antrag::STATUS_ABGELEHNT, $this->loggedInUser->getUserName());
        $this->antrag->setTsEntscheidung(time());
        if (!$this->antrag->save()) {
            $this->smarty->assign('meldung_speichern', 'Fehler beim Ändern des Status. Die E-Mail wurde aber versandt.');
        } else {
            $this->smarty->assign('meldung_speichern', 'Ok: E-Mail versandt und Status geändert.');
        }
    }

    private function aktion_ablehnen_laden()
    {
        if (in_array($this->antrag->getStatus(), [Antrag::STATUS_AUFGENOMMEN, Antrag::STATUS_ABGELEHNT]) && $_POST['formular'] != 'ablehnen') {
            $this->smarty->append('meldungen_laden',
                'Warnung: Der Antragsstatus ist "' . $this->antrag->getStatusReadable() .
                '".'
            );
        }
    }
}

function antraege__laden($parameter)
{
    $lk = new lib_antraege($parameter);
    $lk->exec();
}

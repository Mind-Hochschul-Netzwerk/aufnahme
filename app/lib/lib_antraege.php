<?php
namespace MHN\Aufnahme;

use MHN\Aufnahme\Domain\Model\Email;
use MHN\Aufnahme\Domain\Model\Vote;
use MHN\Aufnahme\Domain\Repository\EmailRepository;
use MHN\Aufnahme\Domain\Repository\UserRepository;
use MHN\Aufnahme\Domain\Repository\VoteRepository;
use MHN\Aufnahme\Service\Configuration;
use PHPMailer;
use Smarty;
use MHN\Aufnahme\Antrag;

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

        $this->antrag->setStatus((int)$_POST['status'], $this->loggedInUser->getId());

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
        global $daten__keys, $daten__keys_checkbox;

        // leere Checkboxen werden nicht gesendet
        foreach ($daten__keys_checkbox as $key) {
            $_POST[$key] = isset($_POST[$key]) ? 'j' : 'n';
        }

        foreach ($daten__keys as $key) {
            // nicht im Formular änderbar:
            if (in_array($key, ['kenntnisnahme_datenverarbeitung', 'kenntnisnahme_datenverarbeitung_text', 'einwilligung_datenverarbeitung', 'einwilligung_datenverarbeitung_text'], true)) {
                continue;
            }
            if (!property_exists($daten, $key)) {
                die('Unbekannte Property in daten: ' . $key);
            }
            if (!isset($_POST[$key])) {
                die('nicht vom Formular gesetzt: ' . $key);
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
        $vote->setUserId($this->loggedInUser->getId());
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
                    $vote = $antrag->getLatestVoteByUserId($this->loggedInUser->getId());

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
            $userids_gevotet = [];
            foreach ($antraege as $antrag) {
                $votes = $antrag->getVotes();
                foreach ($votes as $vote) {
                    $userid = $vote->getUserId();
                    if (!in_array($userid, $userids_gevotet, true)) {
                        $userids_gevotet[] = $userid;
                    }
                }
            }
            $usernames = [];
            foreach ($userids_gevotet as $uid) {
                $user = UserRepository::getInstance()->findOneById((int)$uid);
                if (!$user) {
                    $usernames[$uid] = '(unbekannt)';
                } else {
                    $usernames[$uid] = $user->getUsername();
                }
            }
            $this->smarty->assign('usernames', $usernames);
            $this->smarty->assign('userids_gevotet', $userids_gevotet);
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
        if ($aktion === '') {
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
                $user = UserRepository::getInstance()->findOneById($email->getSenderUserId());
                $emailData[] = [
                    'userName' => ($user !== null) ? $user->getUserName() : 'unbekannt',
                    'time' => $email->getCreationTime()->getTimestamp(),
                    'grund' => ucfirst($email->getGrund()),
                ];
            }
            $this->smarty->assign('mails', $emailData);
        // Aktion (aufnehmen, nachfragen, ablehnen) zu einem Antrag ausführen
        } else {
            $this->smarty->assign('antrag', $this->antrag);
            $this->smarty->assign('heute', Util::tsToDatum(time()));
            $aktion = trim($aktion, '/');

            $mailConfiguration = Configuration::getInstance()->get('mail');
            $this->smarty->assign('absende_email_kand', $mailConfiguration['from']);
            $this->smarty->assign('bcc_email_kand', $mailConfiguration['bcc']);

            $email = $this->antrag->getEMail();
            if (!Util::emailIsValid($email)) {
                $this->smarty->append('meldungen_laden',
                    'Warnung: E-Mail-Adresse des Kandidaten ("' . $email . '") scheint ungültig zu sein.'
                );
            }
            if ($aktion == 'aufnehmen') {
                $this->aktion_aufnehmen_speichern();
                $this->aktion_aufnehmen_laden();
                $this->smarty->assign('innentemplate', 'antraege/aktion/aufnehmen.tpl');
            } elseif ($aktion == 'nachfragen') {
                $this->aktion_nachfragen_speichern();
                $this->aktion_nachfragen_laden();
                $this->smarty->assign('innentemplate', 'antraege/aktion/nachfragen.tpl');
            } elseif ($aktion == 'ablehnen') {
                $this->aktion_ablehnen_speichern();
                $this->aktion_ablehnen_laden();
                $this->smarty->assign('innentemplate', 'antraege/aktion/ablehnen.tpl');
            } else {
                $this->smarty->assign('innentemplate', 'antraege/aktion/unbekannt.tpl');
            }
        }
    }

    /**
     * Sendet eine E-Mail an den Kandidaten, mit den Einstellungen aus configuration.yml (Absender und bcc).
     *
     * @param string $betreff
     * @param string $inhalt
     * @param string $inhalt_alt Mailinhalt für's Archiv (ohn das Benutzerpasswort), falls leer, wird $inhalt verwendet.
     * @param string $grund muss 'aufnahme', 'nachfrage' oder 'ablehnung' sein
     * @throws \PHPMailerException wenn ein Fehler auftritt
     * @return void
     */
    private function sende_email_kand(string $betreff, string $inhalt, string $grund, string $inhalt_alt = '')
    {
        $mailConfiguration = Configuration::getInstance()->get('mail');

        $mail = new PHPMailer(true);
        $mail->From = $mailConfiguration['from'];
        $mail->FromName = 'MHN-Aufnahmekommission';
        $mail->Encoding = 'quoted-printable';
        $mail->CharSet = 'utf-8';
        $mail->Subject = $betreff;
        $db_mail = new Email();
        $db_mail->setAntragId($this->antrag->getID());
        $db_mail->setGrund($grund);
        $db_mail->setSenderUserId($this->loggedInUser->getId());
        $db_mail->setSubject($betreff);
        if ($inhalt_alt == '') {
            $db_mail->setText($inhalt);
        } else {
            $db_mail->setText($inhalt_alt);
        }
        if ($inhalt_alt == '') {//gleichen Inhalt senden:
            $mail->AddAddress($this->antrag->getEMail());
            foreach ($mailConfiguration['bcc'] as $email) {
                $mail->AddBCC($email);
            }
            $mail->Body = $inhalt;
            $mail->Send();
        } else {
            //an den Kand.:
            $mail->AddAddress($this->antrag->getEMail());
            $mail->Body = $inhalt;
            $mail->Send();
            //an die AK:
            $mail->ClearAddresses();
            foreach ($mailConfiguration['bcc'] as $email) {
                $mail->AddAddress($email);
            }
            $mail->Body = $inhalt_alt;
            $mail->Send();
        }
        EmailRepository::getInstance()->add($db_mail);
    }

    /**
     * Die Aktion "aufnahme" verarbeiten
     *
     * @return void
     */
    private function aktion_aufnehmen_speichern()
    {
        if ($_POST['formular'] != 'aufnahme') {
            return;
        }
        $r = $this->antrag->daten->inMitgliederDB();
        if (!$r['success']) {
            $this->smarty->assign('meldung_speichern', 'Fehler beim importieren in Mitglieder-DB: ' . $r['meldung']);
            return;
        }

        $mailtext_orig = $_POST['mailtext'];
        // Aktivierungslink ersetzen:
        $mailtext = str_replace('%aktivierungslink%', $r['aktivierungslink'], $mailtext_orig);

        try {
            $this->sende_email_kand($_POST['betreff'], $mailtext, 'aufnahme', $mailtext_orig);
        } catch (\PHPMailerException $e) {
            $this->smarty->assign('meldung_speichern', 'Fehler beim E-Mail versenden der E-Mail: ' . $e->errorMessage());
            return;
        }

        $this->antrag->setStatus(Antrag::STATUS_AUFGENOMMEN, $this->loggedInUser->getId());
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
        if ($_POST['formular'] != 'nachfrage') {
            return;
        }

        try {
            $this->sende_email_kand($_POST['betreff'], $_POST['mailtext'], 'nachfrage');
        } catch (\PHPMailerException $e) {
            $this->smarty->assign('meldung_speichern', 'Fehler beim E-Mail versenden der E-Mail: ' . $e->errorMessage());
            return;
        }

        $this->antrag->setStatus(Antrag::STATUS_AUF_ANTWORT_WARTEN, $this->loggedInUser->getId());
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

        $this->antrag->setStatus(Antrag::STATUS_ABGELEHNT, $this->loggedInUser->getId());
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

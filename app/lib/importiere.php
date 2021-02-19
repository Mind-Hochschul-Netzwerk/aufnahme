<?php
namespace MHN\Aufnahme;

//liest von der Standardeingabe einen php-serialisierten Datensatz
// für die Aufnahme. Dieser muss enthalten:
// 'dbarray': das Datenbank-Array mit allen (möglichst vielen) mhn_*-Feldern
//    und user_email.
// 'fragen_werte': Antwort auf die "Zusatz"-Fragen (die also nicht im Wiki landen später).
//
// gibt aus: <serialisiertes Array>, wobei das Array enthält:
// 'success': boolean (ok / nicht ok)
// 'meldung': vor Allem falls nicht ok

use MHN\Aufnahme\Service\Configuration;
use PHPMailer;

ob_start();

function ende()
{
    global $result;
    ob_clean();
    print json_encode($result);
    ob_end_flush();
    exit(0);
}

$result = [
    'success' => false,
    'meldung' => 'Unbekannter Fehler',
];

if ($input === false) {
    $result['meldung'] = 'Fehler bei unserialize der Eingabe';
    ende();
}

if (!is_array($input['dbarray']) || $input['dbarray']['mhn_vorname'] == '') {
    $result['meldung'] = 'Fehler in der Eingabe: wichtige Daten nicht vorhanden!';
    ende();
}

if (!is_array($input['fragen_werte'])) {
    $result['meldung'] = 'Fehler in der Eingabe: wichtige Daten nicht vorhanden! (fragen_werte)';
    ende();
}

require_once('globals.php');

if (Sql::getInstance()->linkOk() === false) {
    $result['meldung'] = 'Fehler: konnte nicht mit DB verbinden.';
    ende();
}

$d = Daten::datenFromDbArray($input['dbarray']);
if (!$d->getImport_ok()) {
    $result['meldung'] = 'Fehler: nicht alle Datenfelder gesetzt: ' . print_r($d->getImport_fehlt(), true);
    ende();
}
$a = new Antrag();
$a->setStatus(Antrag::STATUS_BEWERTEN, 0);
$a->setDaten($d);
$a->setFragenWerte($input['fragen_werte']);
$a->setTsAntrag(time());
if (!$a->addThisAntrag()) {
    $result['meldung'] = 'Fehler beim Hinzufügen des Antrags zur Datenbank';
    ende();
}

$mailConfiguration = Configuration::getInstance()->get('mail');

// Eingangsbestätigung an den Kandidaten
$mail = new PHPMailer();
$mail->From = $mailConfiguration['from'];
$mail->FromName = 'MHN-Aufnahmekommission';
$mail->Encoding = 'quoted-printable';
$mail->CharSet = 'utf-8';
$mail->Subject = 'Dein MHN-Mitgliedsantrag ist eingegangen.';
$mail->AddAddress($a->getEMail());

$smarty->assign('geschlecht', $a->daten->mhn_geschlecht);
$smarty->assign('titel', $a->daten->mhn_titel);
$smarty->assign('name', $a->getName());

$mail->Body = $smarty->fetch('mail/eingangsbestaetigung.tpl');

if (!$mail->Send()) {
    $result['meldung']
        = 'Der Antrag wurde gespeichert, aber es konnte keine Eingangsbestätigung an dich versandt werden.';
    ende();
}

$result['success'] = true;
ende();

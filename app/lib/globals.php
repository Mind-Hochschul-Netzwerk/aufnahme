<?php
namespace MHN\Aufnahme;

//kritische Verzeichnisse setzen; schließlich wollen
// wir nicht von anderen Anwendungen ausspioniert werden:
$temp_dir = '/tmp/';

//Dateien, die erzeugt werden, sollen jeden Zugriff haben koennen:
umask('0000');

//Bei Assertions: anzeigen und Abbrechen (da viele Assetions auch als SIcherheitsueberprueung verwendet werden...)
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 1);
assert_options(ASSERT_BAIL, 1);

//============= 1. SMARTY und allgemeines ==================

$smarty = Service\SmartyContainer::getInstance()->getSmarty();

$smarty->assign('self', $_SERVER['REQUEST_URI']);

// Beachte: der Unterschied zwischen 'offen' und 'entschieden' wird in antrag.php in alleOffenenAntraege und
// alleEntschiedenenAntraege festgelegt!
$global_status = [
    Antrag::STATUS_NEU_BEWERTEN => 'Neu bewerten',
    Antrag::STATUS_BEWERTEN  => 'Bewerten',
    Antrag::STATUS_NACHFRAGEN  => 'Nachfragen',
    Antrag::STATUS_AUFNEHMEN => 'Aufnehmen',
    Antrag::STATUS_ABLEHNEN => 'Ablehnen',
    Antrag::STATUS_AUF_ANTWORT_WARTEN => 'Auf Antwort warten',
    Antrag::STATUS_AUFGENOMMEN => 'Aktivierungslink verschickt',
    Antrag::STATUS_AKTIVIERT => 'Mitgliedskonto aktiviert',
    Antrag::STATUS_ABGELEHNT => 'Abgelehnt',
];

$global_fragen = [ //vom Aufnahmeformular auf der Homepage
    'MHN_Beitragen' => 'Was möchtest du zu MHN beitragen?',
    'MHN_Interesse' => 'Was hat Dein Interesse an MHN geweckt?',
    'MHN_Vorstellung' => 'Welche Vorstellung und welche Erwartungen hast Du bislang von MHN?',
    'MHN_Kennen' => 'Welche MHN-Mitglieder kennst du persönlich?',
];

//============== 5. Sanity-Checks =========================
if (!(is_writable($temp_dir) and is_dir($temp_dir))) {
    die('Kann nicht in Tempverzeichnis schreiben. Bitte Einstellungen in config.php und Schreibberechtigung prüfen.');
}

//================= 6. Wartungsarbeiten ==========================
//Es gibt einige Wartungsarbeiten, für die ein cronjob overkill wäre. Daher
// werden diese ggf. hier erledigt.

$letztewartung = filemtime($temp_dir . 'letztewartung');
if ($letztewartung === false) {
    $letztewartung = 0;
}

//Wie lange liegt das letzte Mal zurück? Falls länger als 2 Stunden: Wartung durchführen:
if (time() - date('U', $letztewartung) > 60 * 60 * 2) {
    //Nun Wartungsarbeiten durchführen:
    require_once('wartung.php');

    //Modifikationszeit setzen:
    $fh = fopen($temp_dir . 'letztewartung', 'w');
    fwrite($fh, date('YmdHis'));
    fclose($fh);
}

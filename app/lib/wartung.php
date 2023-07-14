<?php
namespace MHN\Aufnahme;

//diese Datei wird von globals.php ca. alle zwei Stunden eingebunden.
//ein Cron-Job könnte die Hauptseite einmal am Tag aufrufen ...

use MHN\Aufnahme\Antrag;
use MHN\Aufnahme\Domain\Repository\UserRepository;
use MHN\Aufnahme\Domain\Repository\TemplateRepository;
use MHN\Aufnahme\Service\EmailService;

const MAINTENANCE_LOCKFILE = '/tmp/letztewartung';

/**
 * sendet an alle Mitglieder der Aufnahmekommission eine Erinnerung mit einer Liste von Anträgen,
 * die folgende Kriterien erfüllen:
 * - status ist auf "Bewerten"
 * - Antrag ist älter als eine Woche.
 */
function wartung_sende_erinnerung(): void
{
    $zu_erinnernde = [];
    foreach (Antrag::getAllByStatus(Antrag::STATUS_BEWERTEN) as $antrag) {
        $t_diff = time() - $antrag->getTsAntrag();
        $t_diff_erinnerung = time() - $antrag->getTsErinnerung();
        if ($t_diff_erinnerung > 9 * 60 * 60 * 24 && $t_diff > 9 * 60 * 60 * 24) {
            $zu_erinnernde[] = $antrag;
        }
    }
    if (!$zu_erinnernde) {
        return;
    }

    $names = [];
    foreach ($zu_erinnernde as $antrag) {
        $antrag->setTsErinnerung(time());
        $antrag->save();
        $names[] = $antrag->getName();
    }

    $mailTemplate = TemplateRepository::getInstance()->getOneByName('teamReminder');
    UserRepository::getInstance()->sendEmailToAll($mailTemplate->getSubject(), $mailTemplate->getFinalText([
        'namen' => implode("\n", $names),
    ]));
}

function sendActivationReminders(): void
{
    $userMailTemplate = TemplateRepository::getInstance()->getOneByName('userActivationReminder');
    $teamMailTemplate = TemplateRepository::getInstance()->getOneByName('teamActivationReminder');

    foreach (Antrag::getAllByStatus(Antrag::STATUS_AUFGENOMMEN) as $antrag) {
        $t_diff = time() - $antrag->getTsEntscheidung();
        $t_diff_erinnerung = time() - $antrag->getTsErinnerung();

        if ($t_diff_erinnerung < 9 * 60 * 60 * 24 || $t_diff < 9 * 60 * 60 * 24) {
           continue;
        }

        $ablaufDatum = Util::tsToDatum($antrag->getTsEntscheidung() + 3600*24*7*12);
        EmailService::getInstance()->send($antrag->getEmail(), $userMailTemplate->getSubject(), $userMailTemplate->getFinalText([
            'vorname' => $antrag->getVorname(),
            'mailDatum' => $antrag->getDatumEntscheidung(),
            'ablaufDatum' => $ablaufDatum,
            'url' => $antrag->getActivationUrl(),
        ]));
        UserRepository::getInstance()->sendEmailToAll($teamMailTemplate->getSubject(), $teamMailTemplate->getFinalText([
            'name' => $antrag->getName(),
            'antragsnummer' => $antrag->getId(),
            'ablaufDatum' => $ablaufDatum,
        ]));
        $antrag->setTsErinnerung(time());
        $antrag->save();
    }

}

// Wie lange liegt das letzte Mal zurück? Falls länger als 2 Stunden: Wartung durchführen:
if (!is_file(MAINTENANCE_LOCKFILE) || time() - date('U', filemtime(MAINTENANCE_LOCKFILE)) > 60 * 60 * 2) {
    Antrag::deleteOld();
    wartung_sende_erinnerung();
    sendActivationReminders();

    touch(MAINTENANCE_LOCKFILE);
}

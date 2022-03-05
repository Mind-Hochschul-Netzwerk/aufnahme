<?php
namespace MHN\Aufnahme;

//diese Datei wird von globals.php ca. alle zwei Stunden eingebunden.
//ein Cron-Job könnte die Hauptseite einmal am Tag aufrufen ...

use MHN\Aufnahme\Antrag;
use MHN\Aufnahme\Domain\Repository\UserRepository;
use MHN\Aufnahme\Domain\Repository\TemplateRepository;

/**
 * sendet an alle Mitglieder der Aufnahmekommission eine Erinnerung mit einer Liste von Anträgen,
 * die folgende Kriterien erfüllen:
 * - status ist auf "Bewerten"
 * - Antrag ist älter als eine Woche.
 */
function wartung_sende_erinnerung(): void
{
    $antraege = Antrag::alleOffenenAntraege();
    $zu_erinnernde = [];     //die über 9 Tage
    $ggf_zu_erinnernde = []; // die über 7 Tage. Werden nur verschickt, falls es auch einen über 9 gibt ...
    foreach ($antraege as $antrag) {
        if ($antrag->getStatus() != Antrag::STATUS_BEWERTEN) {
            continue;
        }
        $t_diff = time() - $antrag->getTsAntrag();
        $t_diff_erinnerung = time() - $antrag->getTsErinnerung();
        if ($t_diff_erinnerung > 9 * 60 * 60 * 24 && $t_diff > 9 * 60 * 60 * 24) {
            array_push($zu_erinnernde, $antrag);
        } elseif ($t_diff > 7 * 60 * 60 * 24 && $t_diff_erinnerung > 7 * 60 * 60 * 24) {
            array_push($ggf_zu_erinnernde, $antrag);
        }
    }
    if (count($zu_erinnernde) == 0) {
        return;
    }

    $names = [];
    foreach (array_merge($zu_erinnernde, $ggf_zu_erinnernde) as $a) {
        $a->setTsErinnerung(time());
        $a->save();
        $names[] = $a->getName();
    }

    $mailTemplate = TemplateRepository::getInstance()->getOneByName('teamReminder');
    $text = $mailTemplate->getFinalText([
        'namen' => implode("\n", $names),
    ]);
    UserRepository::getInstance()->sendEmailToAll($mailTemplate->getSubject(), $text);
}

wartung_sende_erinnerung();

Antrag::deleteOld();

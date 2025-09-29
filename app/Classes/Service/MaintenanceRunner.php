<?php
namespace App\Service;

use App\Model\Antrag;
use App\Repository\AntragRepository;
use App\Repository\UserRepository;
use App\Repository\TemplateRepository;
use App\Service\EmailService;
use App\Util;

class MaintenanceRunner {
    const MAINTENANCE_LOCKFILE = '/tmp/letztewartung';

    public function __construct(
        private AntragRepository $antragRepository,
        private TemplateRepository $templateRepository,
        private UserRepository $userRepository,
        private EmailService $emailService,
    ) {}

    public function run() {
        // Wie lange liegt das letzte Mal zurück? Falls länger als 2 Stunden: Wartung durchführen:
        if (!is_file(self::MAINTENANCE_LOCKFILE) || time() - date('U', filemtime(self::MAINTENANCE_LOCKFILE)) > 60 * 60 * 2) {
            $this->antragRepository->deleteOld();
            $this->sendeErinnerung();
            $this->sendActivationReminders();

            touch(self::MAINTENANCE_LOCKFILE);
        }
    }

    /**
     * sendet an alle Mitglieder der Aufnahmekommission eine Erinnerung mit einer Liste von Anträgen,
     * die folgende Kriterien erfüllen:
     * - status ist auf "Bewerten"
     * - Antrag ist älter als eine Woche.
     */
    private function sendeErinnerung(): void
    {
        $zu_erinnernde = [];
        foreach ($this->antragRepository->getAllByStatus(Antrag::STATUS_BEWERTEN) as $antrag) {
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
            $this->antragRepository->save($antrag);
            $names[] = $antrag->getName();
        }

        $mailTemplate = $this->templateRepository->getOneByName('teamReminder');
        $this->userRepository->sendEmailToAll($mailTemplate->getSubject(), $mailTemplate->getFinalText([
            'namen' => implode("\n", $names),
        ]));
    }

    private function sendActivationReminders(): void
    {
        $userMailTemplate = $this->templateRepository->getOneByName('userActivationReminder');
        $teamMailTemplate = $this->templateRepository->getOneByName('teamActivationReminder');

        foreach ($this->antragRepository->getAllByStatus(Antrag::STATUS_AUFGENOMMEN) as $antrag) {
            $t_diff = time() - $antrag->getTsEntscheidung();
            $t_diff_erinnerung = time() - $antrag->getTsErinnerung();

            if ($t_diff_erinnerung < 9 * 60 * 60 * 24 || $t_diff < 9 * 60 * 60 * 24) {
                continue;
            }

            $ablaufDatum = Util::tsToDatum($antrag->getTsEntscheidung() + 3600*24*7*12);
            $this->emailService->send($antrag->getEmail(), $userMailTemplate->getSubject(), $userMailTemplate->getFinalText([
                'vorname' => $antrag->getVorname(),
                'mailDatum' => $antrag->getDatumEntscheidung(),
                'ablaufDatum' => $ablaufDatum,
                'url' => $antrag->getActivationUrl(),
            ]));
            $this->userRepository->sendEmailToAll($teamMailTemplate->getSubject(), $teamMailTemplate->getFinalText([
                'name' => $antrag->getName(),
                'antragsnummer' => $antrag->getId(),
                'ablaufDatum' => $ablaufDatum,
            ]));
            $antrag->setTsErinnerung(time());
            $this->antragRepository->save($antrag);
        }
    }
}

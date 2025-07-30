<?php
namespace App\Controller;

use App\Model\Antrag;
use App\Model\Email;
use App\Repository\AntragRepository;
use App\Repository\EmailRepository;
use App\Repository\TemplateRepository;
use App\Repository\UserRepository;
use App\Service\CurrentUser;
use App\Service\EmailService;
use App\Service\Tpl;
use App\Util;
use Hengeb\Router\Attribute\RequestValue;
use Hengeb\Router\Attribute\Route;
use Hengeb\Router\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EmailController extends Controller
{
    private bool $isSubmitted = false;

    public function __construct(
        protected Request $request,
        private CurrentUser $currentUser,
        private EmailRepository $repository,
    )
    {
    }

    #[Route('GET /mails/{id=>antrag}/{timestamp}', ['loggedIn' => true])]
    public function show(Antrag $antrag, int $timestamp): Response
    {
        $mail = $this->repository->findOneByAntragAndTimestamp($antrag, $timestamp);
        if (!$mail) {
            throw new NotFoundException();
        }

        $user = UserRepository::getInstance()->findOneByUserName($mail->getSenderUserName());

        return $this->render('EmailController/email', [
            'antrag' => $antrag,
            'userName' => ($user !== null) ? $user->getRealName() : 'unbekannt',
            'time' => $mail->getCreationTime(),
            'grund' => ucfirst($mail->getGrund()),
            'subject' => $mail->getSubject(),
            'text' => $mail->getText(),
        ]);
    }

    #[Route('GET /antraege/{id=>antrag}/{aufnehmen|nachfragen|ablehnen:aktion}', ['loggedIn' => true])]
    public function aktion(Antrag $antrag, string $aktion): Response
    {
        Tpl::getInstance()->set('heute', Util::tsToDatum(time()));
        Tpl::getInstance()->set('absende_email_kand', getenv('FROM_ADDRESS'));

        if (in_array($antrag->getStatus(), [
            Antrag::STATUS_AUFGENOMMEN, Antrag::STATUS_ABGELEHNT, Antrag::STATUS_AKTIVIERT
        ]) && !$this->isSubmitted) {
            Tpl::getInstance()->set('meldungen_laden',
                'Warnung: Der Antragsstatus ist bereits "' . $antrag->getStatusReadable() . '".'
            );
        }

        Tpl::getInstance()->set('aktion', $aktion);

        $template = TemplateRepository::getInstance()->getOneByName($aktion);
        Tpl::getInstance()->set('mailSubject', $template->getSubject());
        $replacements = [
            'vorname' => $antrag->getVorname(),
            'autor' => $this->currentUser->getRealName(),
        ];
        if ($aktion === 'nachfragen') {
            $fragen = array_filter(array_map(function ($vote) {
                return trim($vote->getNachfrage());
            }, $antrag->getVotes()));
            $replacements['fragen'] = implode("\n\n", $fragen);
        }
        Tpl::getInstance()->set('mailText', $template->getFinalText($replacements));
        return $this->render('AntragController/aktion', [
            'antrag' => $antrag,
            'aktion' => $aktion,
        ]);
    }

    #[Route('POST /antraege/{id=>antrag}/{aufnehmen|nachfragen|ablehnen:aktion}', ['loggedIn' => true])]
    public function submitAktion(Antrag $antrag, string $aktion, AntragRepository $antragRepository, #[RequestValue] string $mailtext, #[RequestValue] string $betreff): Response
    {
        $this->isSubmitted = true;
        $mailtext_orig = $mailtext;

        switch ($aktion) {
            case 'aufnehmen':
                $antrag->setStatus(Antrag::STATUS_AUFGENOMMEN, $this->currentUser->getUserName());
                $antrag->setTsEntscheidung(time());
                $mailtext = str_replace('{$url}', $antrag->getActivationUrl(), $mailtext_orig);
                break;
            case 'nachfragen':
                $antrag->setStatus(Antrag::STATUS_AUF_ANTWORT_WARTEN, $this->currentUser->getUserName());
                $antrag->setTsNachfrage(time());
                $mailtext = str_replace('{$url}', $antrag->getEditUrl(), $mailtext_orig);
                break;
            case 'ablehnen':
                $antrag->setStatus(Antrag::STATUS_ABGELEHNT, $this->currentUser->getUserName());
                $antrag->setTsEntscheidung(time());
                break;
        }

        try {
            $this->sende_email_kand($betreff, $mailtext, $aktion, $mailtext_orig, $antrag);
        } catch (\PHPMailerException $e) {
            Tpl::getInstance()->set('meldung_speichern', 'Fehler beim E-Mail versenden der E-Mail: ' . $e->errorMessage());
            return $this->aktion($antrag, $aktion);
        }

        $antragRepository->save($antrag);
        Tpl::getInstance()->set('meldung_speichern', 'Ok: E-Mail versandt und Status geändert.');
        return $this->aktion($antrag, $aktion);
    }

    /**
     * Sendet eine E-Mail an den Kandidaten
     *
     * @param string $betreff
     * @param string $inhalt
     * @param string $inhalt_alt Mailinhalt für's Archiv (ohn das Benutzerpasswort), falls leer, wird $inhalt verwendet.
     * @param string $aktion ('aufnehmen', 'nachfragen' oder 'ablehnen')
     * @return void
     */
    private function sende_email_kand(string $betreff, string $inhalt, string $aktion, string $inhalt_alt, Antrag $antrag)
    {
        $db_mail = new Email();
        $db_mail->setAntragId($antrag->getID());
        $db_mail->setGrund(match ($aktion) {
            'aufnehmen' => 'aufnahme',
            'nachfragen' => 'nachfrage',
            'ablehnen' => 'ablehnung',
        });
        $db_mail->setSenderUserName($this->currentUser->getUserName());
        $db_mail->setSubject($betreff);
        $db_mail->setText($inhalt_alt ? $inhalt_alt : $inhalt);
        EmailService::getInstance()->send($antrag->getEMail(), $betreff, $inhalt);
        UserRepository::getInstance()->sendEmailToAll($betreff, $inhalt_alt ? $inhalt_alt : $inhalt);
        $this->repository->add($db_mail);
    }
}

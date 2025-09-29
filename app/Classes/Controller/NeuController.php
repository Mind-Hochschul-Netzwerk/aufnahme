<?php
namespace App\Controller;

use App\Model\Antrag;
use App\Service\EmailService;
use App\Model\FormData;
use App\Repository\AntragRepository;
use App\Repository\UserRepository;
use App\Repository\TemplateRepository;
use App\Repository\VoteRepository;
use Hengeb\Router\Attribute\CheckCsrfToken;
use Hengeb\Router\Attribute\PublicAccess;
use Hengeb\Router\Attribute\RequestValue;
use Hengeb\Router\Attribute\Route;
use Hengeb\Router\Exception\InvalidUserDataException;
use Hengeb\Token\Token;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;

class NeuController extends Controller
{
    private FormData $werte;
    private array $agreements;

    public function __construct(
        private AntragRepository $antragRepository,
        private TemplateRepository $templateRepository,
        private UserRepository $userRepository,
        private VoteRepository $voteRepository,
        private EmailService $emailService,
    ) {
        $this->setTemplateVariable('introTemplate', $this->templateRepository->getOneByName('intro'));
        $this->loadAgreements();
        $this->werte = new FormData();
        $this->setTemplateVariable('werte', $this->werte->toArray());
    }

    private function loadAgreements(): void
    {
        $this->agreements = [
            'einwilligung' => json_decode(file_get_contents('http://mitglieder:8080/agreements/text/Einwilligung'), associative: true),
            'kenntnisnahme' => json_decode(file_get_contents('http://mitglieder:8080/agreements/text/Kenntnisnahme'), associative: true),
        ];
        $this->setTemplateVariable('agreements', $this->agreements);
    }

    #[Route('GET /antrag'), PublicAccess]
    public function emailForm(): Response
    {
        return $this->render('NeuController/emailForm', ['email' => '']);
    }

    #[Route('POST /antrag'), PublicAccess]
    public function initEmailAuth(#[RequestValue] string $email): Response
    {
        if ($this->antragRepository->findOneByEmail($email)) {
            $this->setTemplateVariable('emailUsed', true);
            return $this->emailForm();
        }

        $token = Token::encode([$email, time()], '', getenv('TOKEN_KEY'));
        $mailTemplate = $this->templateRepository->getOneByName('emailToken');
        $text = $mailTemplate->getFinalText([
            'url' => 'www.' . getenv('DOMAINNAME') . "/aufnahme?token=$token",
        ]);
        $this->emailService->send($email, $mailTemplate->getSubject(), $text);

        return $this->render('NeuController/initEmailAuth');
    }

    private function decodeEmailToken(string $token): void
    {
        try {
            $mail = Token::decode($token, function ($data) {
                list($email, $time) = $data;
                if (time() - $time > 3600*24*7) {
                    throw new \RuntimeException("token expired");
                }
                if ($this->antragRepository->findOneByEmail($email)) {
                    $this->setTemplateVariable('emailUsed', true);
                    throw new \RuntimeException("email used");
                }
                return '';
            }, getenv('TOKEN_KEY'))[0];
            $this->werte->set('user_email', $mail);
        } catch (\Exception $e) {
            throw new InvalidUserDataException('Die Link zur Bestätigung der Mailadrese ist ungültig. Er ist abgelaufen, wurde bereits verwendet oder wurde falsch aus der E-Mail kopiert.');
        }
    }

    #[Route('GET /antrag?token={token}'), PublicAccess]
    public function form(string $token): Response
    {
        $this->decodeEmailToken($token);

        $this->agreements['einwilligung']['token'] = $this->createAgreementToken('einwilligung');
        $this->agreements['kenntnisnahme']['token'] = $this->createAgreementToken('kenntnisnahme');

        return $this->render('NeuController/form', [
            'werte' => $this->werte->toArray(),
            'agreements' => $this->agreements,
        ]);
    }

    private function createAgreementToken(string $agreementName): string
    {
        return Token::encode(
            [$this->agreements[$agreementName]['version'], time()],
            $agreementName . $this->werte->getEmail(),
            getenv('TOKEN_KEY'),
        );
    }

    private function getVersionFromAgreementToken(string $token, string $agreementName): int
    {
        return Token::decode($token, function ($payload) use ($agreementName) {
            [$version, $time] = $payload;
            if ($time + 24*3600 < time()) {
                throw new InvalidUserDataException('Die Anfrage ist veraltet.', 1759183230);
            }
            return $agreementName . $this->werte->getEmail();
        }, getenv('TOKEN_KEY'))[0];
    }

    #[Route('POST /antrag?token={token}'), PublicAccess, CheckCsrfToken(false)]
    public function handleActionAntrag(
        string $token,
        ParameterBag $submittedData,
        #[RequestValue] string $kenntnisnahme_datenverarbeitung = '',
        #[RequestValue] string $einwilligung_datenverarbeitung = '',
    ): Response
    {
        $this->decodeEmailToken($token);

        $dataIsValid = true;

        if (!$kenntnisnahme_datenverarbeitung || !$einwilligung_datenverarbeitung) {
            $this->setTemplateVariable('datenschutzInfo', true);
            $dataIsValid = false;
        }

        $kenntnisnahme_version = $this->getVersionFromAgreementToken($kenntnisnahme_datenverarbeitung, 'kenntnisnahme');
        $einwilligung_version = $this->getVersionFromAgreementToken($einwilligung_datenverarbeitung, 'einwilligung');

        $dataIsValid &= $this->werte->updateFromForm($submittedData);

        $this->werte->set('kenntnisnahme_datenverarbeitung', new \DateTime());
        $this->werte->set('kenntnisnahme_datenverarbeitung_text', $kenntnisnahme_version);
        $this->werte->set('einwilligung_datenverarbeitung', new \DateTime());
        $this->werte->set('einwilligung_datenverarbeitung_text', $einwilligung_version);

        $birthday = FormData::parseBirthdayInput($submittedData->get('mhn_geburtstag'));
        if ($birthday) {
            $this->werte->set('mhn_geburtstag', $birthday);
        } else {
            $this->setTemplateVariable('invalidBirthday', true);
            $dataIsValid = false;
        }

        if (!$dataIsValid) {
            return $this->form($token);
        }

        $a = new Antrag($this->voteRepository);
        $a->setStatus(Antrag::STATUS_BEWERTEN, 0);
        $a->setDaten($this->werte);
        $a->setTsAntrag(time());
        $a->setTsStatusaenderung(time());

        try {
            $this->antragRepository->add($a);
        } catch (\RuntimeException $e) {
            throw new \RuntimeException('failed to save. Error message: ' . $e->getMessage(), 1614464427);
        }

        $this->userRepository->sendEmailToAll('Neuer Antrag', 'Im MHN-Aufnahmetool ist ein neuer Mitgliedsantrag eingegangen.');

        return $this->redirect('/antrag/success/?' . $this->queryStringForEmbedding());
    }

    #[Route('GET /antrag/success'), PublicAccess]
    public function success(): Response
    {
        return $this->render('NeuController/success');
    }
}

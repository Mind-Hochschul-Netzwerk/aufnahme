<?php
namespace App\Controller;

use App\Model\Antrag;
use App\Service\EmailService;
use App\Model\FormData;
use App\Repository\AntragRepository;
use App\Repository\UserRepository;
use App\Repository\TemplateRepository;
use App\Service\CurrentUser;
use App\Service\Tpl;
use Hengeb\Router\Attribute\RequestValue;
use Hengeb\Router\Attribute\Route;
use Hengeb\Router\Exception\InvalidUserDataException;
use Hengeb\Token\Token;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NeuController extends Controller
{
    private FormData $werte;

    public function __construct(
        protected Request $request,
        private CurrentUser $currentUser,
    )
    {
        Tpl::getInstance()->set('introTemplate', TemplateRepository::getInstance()->getOneByName('intro'));
        Tpl::getInstance()->set('kenntnisnahmeTemplate', TemplateRepository::getInstance()->getOneByName('kenntnisnahme'));
        Tpl::getInstance()->set('einwilligungTemplate', TemplateRepository::getInstance()->getOneByName('einwilligung'));
        $this->werte = new FormData();
        Tpl::getInstance()->set('werte', $this->werte->toArray());
    }

    #[Route('GET /antrag', allow: true)]
    public function emailForm(): Response
    {
        return $this->render('NeuController/emailForm', ['email' => '']);
    }

    #[Route('POST /antrag', allow: true)]
    public function initEmailAuth(#[RequestValue] string $email): Response
    {
        if (AntragRepository::getInstance()->findOneByEmail($email)) {
            Tpl::getInstance()->set('emailUsed', true);
            return $this->emailForm();
        }

        $token = Token::encode([$email, time()], '', getenv('TOKEN_KEY'));
        $mailTemplate = TemplateRepository::getInstance()->getOneByName('emailToken');
        $text = $mailTemplate->getFinalText([
            'url' => 'www.' . getenv('DOMAINNAME') . "/aufnahme?token=$token",
        ]);
        EmailService::getInstance()->send($email, $mailTemplate->getSubject(), $text);

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
                if (AntragRepository::getInstance()->findOneByEmail($email)) {
                    Tpl::getInstance()->set('emailUsed', true);
                    throw new \RuntimeException("email used");
                }
                return '';
            }, getenv('TOKEN_KEY'))[0];
            $this->werte->set('user_email', $mail);
        } catch (\Exception $e) {
            throw new InvalidUserDataException('Die Link zur Bestätigung der Mailadrese ist ungültig. Er ist abgelaufen, wurde bereits verwendet oder wurde falsch aus der E-Mail kopiert.');
        }
    }

    #[Route('GET /antrag?token={token}', allow: true)]
    public function form(string $token): Response
    {
        $this->decodeEmailToken($token);
        return $this->render('NeuController/form', [
            'werte' => $this->werte->toArray(),
        ]);
    }

    #[Route('POST /antrag?token={token}', allow: true)]
    public function handleActionAntrag(
        string $token,
        ParameterBag $submittedData,
        #[RequestValue] bool $kenntnisnahme_datenverarbeitung = false,
        #[RequestValue] $einwilligung_datenverarbeitung = false
    ): Response
    {
        $this->decodeEmailToken($token);

        $dataIsValid = true;

        if (!$kenntnisnahme_datenverarbeitung || !$einwilligung_datenverarbeitung) {
            Tpl::getInstance()->set('datenschutzInfo', true);
            $dataIsValid = false;
        }

        $dataIsValid &= $this->werte->updateFromForm($submittedData);

        $this->werte->set('kenntnisnahme_datenverarbeitung', new \DateTime());
        $this->werte->set('kenntnisnahme_datenverarbeitung_text', TemplateRepository::getInstance()->getOneByName('kenntnisnahme')->getFinalText());
        $this->werte->set('einwilligung_datenverarbeitung', new \DateTime());
        $this->werte->set('einwilligung_datenverarbeitung_text',  TemplateRepository::getInstance()->getOneByName('einwilligung')->getFinalText());

        $birthday = FormData::parseBirthdayInput($submittedData->get('mhn_geburtstag'));
        if ($birthday) {
            $this->werte->set('mhn_geburtstag', $birthday);
        } else {
            Tpl::getInstance()->set('invalidBirthday', true);
            $dataIsValid = false;
        }

        if (!$dataIsValid) {
            return $this->form($token);
        }

        $a = new Antrag();
        $a->setStatus(Antrag::STATUS_BEWERTEN, 0);
        $a->setDaten($this->werte);
        $a->setTsAntrag(time());

        try {
            AntragRepository::getInstance()->add($a);
        } catch (\RuntimeException $e) {
            throw new \RuntimeException('failed to save. Error message: ' . $e->getMessage(), 1614464427);
        }

        UserRepository::getInstance()->sendEmailToAll('Neuer Antrag', 'Im MHN-Aufnahmetool ist ein neuer Mitgliedsantrag eingegangen.');

        return $this->redirect('/antrag/success/?' . $this->queryStringForEmbedding());
    }

    #[Route('GET /antrag/success', allow: true)]
    public function success(): Response
    {
        return $this->render('NeuController/success');
    }
}

<?php
namespace App;

use App\Service\Token;
use App\Service\EmailService;
use App\Domain\Model\FormData;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\TemplateRepository;

class NeuController
{
    private $smarty = null;

    private $werte = null;
    private $token = '';

    public function handleRequest(): void
    {
        $this->smarty = Service\SmartyContainer::getInstance()->getSmarty();

        $this->smarty->assign('introTemplate', TemplateRepository::getInstance()->getOneByName('intro'));
        $this->smarty->assign('kenntnisnahmeTemplate', TemplateRepository::getInstance()->getOneByName('kenntnisnahme'));
        $this->smarty->assign('einwilligungTemplate', TemplateRepository::getInstance()->getOneByName('einwilligung'));

        $this->werte = new FormData();

        $this->smarty->assign('werte', $this->werte->toArray());

        if (!empty($_REQUEST['actionEmail'])) {
            if ($this->initEmailAuth()) {
                return;
            }
        }

        if (!empty($_GET['token'])) {
            $this->decodeEmailToken((string) $_GET['token']);
        }

        if (!($this->werte->getEmail())) {
            $this->smarty->assign('innentemplate', 'NeuController/emailForm.tpl');
            return;
        }

        if (!empty($_REQUEST['actionAntrag'])) {
            if ($this->handleActionAntrag()) {
                return;
            }
        }

        $this->smarty->assign('werte', $this->werte->toArray());

        $this->smarty->assign('innentemplate', 'NeuController/form.tpl');
    }

    private function initEmailAuth(): bool
    {
        $email = (string) $_REQUEST['email'] ?? '';
        if (!$email) {
            return false;
        }

        if (Antrag::findOneByEmail($email)) {
            $this->smarty->assign('emailUsed', true);
            return false;
        }

        $token = Token::encode([$email, time()], '', getenv('TOKEN_KEY'));
        $mailTemplate = TemplateRepository::getInstance()->getOneByName('emailToken');
        $text = $mailTemplate->getFinalText([
            'url' => 'www.' . getenv('DOMAINNAME') . "/aufnahme?token=$token",
        ]);
        EmailService::getInstance()->send($email, $mailTemplate->getSubject(), $text);

        $this->smarty->assign('innentemplate', 'NeuController/initEmailAuth.tpl');
        return true;
    }


    private function decodeEmailToken(string $token): void
    {
        try {
            $mail = Token::decode($token, function ($data) {
                list($email, $time) = $data;
                if (time() - $time > 3600*24*7) {
                    throw new \RuntimeException("token expired");
                }
                if (Antrag::findOneByEmail($email)) {
                    $this->smarty->assign('emailUsed', true);
                    throw new \RuntimeException("email used");
                }
                return '';
            }, getenv('TOKEN_KEY'))[0];
            $this->werte->set('user_email', $mail);
        } catch (\Exception $e) {
            $this->smarty->assign('tokenInvalid', true);
        }
    }

    private function handleActionAntrag(): bool
    {
        $dataIsValid = true;

        if (empty($_REQUEST['kenntnisnahme_datenverarbeitung']) || empty($_REQUEST['einwilligung_datenverarbeitung'])) {
            $this->smarty->assign('datenschutzInfo', true);
            $dataIsValid = false;
        }

        if (!$this->werte->getEmail()) {
            throw new \RuntimeException('user_email is not set, should be set by NeuController::decodeEmailToken()');
        }

        $dataIsValid &= $this->werte->updateFromForm($this->smarty);

        $this->werte->set('kenntnisnahme_datenverarbeitung', new \DateTime());
        $this->werte->set('kenntnisnahme_datenverarbeitung_text', TemplateRepository::getInstance()->getOneByName('kenntnisnahme')->getFinalText());
        $this->werte->set('einwilligung_datenverarbeitung', new \DateTime());
        $this->werte->set('einwilligung_datenverarbeitung_text',  TemplateRepository::getInstance()->getOneByName('einwilligung')->getFinalText());

        if (!$dataIsValid) {
            return false;
        }

        $a = new Antrag();
        $a->setStatus(Antrag::STATUS_BEWERTEN, 0);
        $a->setDaten($this->werte);
        $a->setTsAntrag(time());

        try {
            $a->addThisAntrag();
        } catch (\RuntimeException $e) {
            throw new \RuntimeException('failed to save. Error message: ' . $e->getMessage(), 1614464427);
        }

        UserRepository::getInstance()->sendEmailToAll('Neuer Antrag', 'Im MHN-Aufnahmetool ist ein neuer Mitgliedsantrag eingegangen.');

        $this->smarty->assign('innentemplate', 'NeuController/success.tpl');

        return true;
    }
}

function NeuController__handleRequest()
{
    $instance = new NeuController();
    $instance->handleRequest();
}

<?php
namespace MHN\Aufnahme;

use MHN\Aufnahme\Daten;
use MHN\Aufnahme\Service\Token;
use MHN\Aufnahme\Service\EmailService;
use MHN\Aufnahme\Domain\Repository\UserRepository;

class NeuController
{
    private $smarty = null;

    private $fragen = [];
    private $fragenWerte = [];
    private $werte = null;
    private $token = '';

    public function handleRequest(): void
    {
        $this->smarty = Service\SmartyContainer::getInstance()->getSmarty();
        $this->werte = (new Daten())->toArray();
        $this->fragen = $GLOBALS['global_fragen'];
        foreach ($this->fragen as $k=>$v) {
            $this->fragenWerte[$k] = '';
        }

        $this->smarty->assign('werte', $this->werte);
        $this->smarty->assign('fragen', $this->fragen);
        $this->smarty->assign('fragen_werte', $this->fragenWerte);

        if (!empty($_REQUEST['actionEmail'])) {
            if ($this->initEmailAuth()) {
                return;
            }
        }

        if (!empty($_GET['token'])) {
            $this->decodeEmailToken((string) $_GET['token']);
        }

        if (!($this->werte['user_email'])) {
            $this->smarty->assign('innentemplate', 'NeuController/emailForm.tpl');
            return;
        }

        if (!empty($_REQUEST['actionAntrag'])) {
            if ($this->handleActionAntrag()) {
                return;
            }
        }

        $this->smarty->assign('werte', $this->werte);
        $this->smarty->assign('fragen_werte', $this->fragenWerte);

        $this->smarty->assign('innentemplate', 'NeuController/form.tpl');
    }

    private function initEmailAuth(): bool
    {
        $email = (string) $_REQUEST['email'] ?? '';
        if (!$email) {
            return false;
        }

        if (Daten::findDatenByEmail($email)) {
            $this->smarty->assign('emailUsed', true);
            return false;
        }

        $token = Token::encode([$email, time()], '', getenv('TOKEN_KEY'));
        $this->smarty->assign('url', 'https://aufnahme.' . getenv('DOMAINNAME') . '/antrag/?token=' . $token);
        $text = $this->smarty->fetch('mails/emailToken.tpl');

        EmailService::getInstance()->send($email, 'Dein MHN-Mitgliedsantrag', $text);

        $this->smarty->assign('innentemplate', 'NeuController/initEmailAuth.tpl');
        return true;
    }

    private function decodeEmailToken(string $token): void
    {
        try {
            $this->werte['user_email'] = Token::decode($token, function ($data) {
                list($email, $time) = $data;
                if (time() - $time > 3600*24*7) {
                    throw new \RuntimeException("token expired");
                }
                if (Daten::findDatenByEmail($email)) {
                    $this->smarty->assign('emailUsed', true);
                    throw new \RuntimeException("email used");
                }
                return '';
            }, getenv('TOKEN_KEY'))[0];
        } catch (\Exception $e) {
            $this->smarty->assign('tokenInvalid', true);
        }
    }

    private function handleActionAntrag(): bool
    {
        if (empty($_REQUEST['kenntnisnahme_datenverarbeitung']) || empty($_REQUEST['einwilligung_datenverarbeitung'])) {
            $this->smarty->assign('datenschutzInfo', true);
            return false;
        }

        foreach ($this->werte as $k=>$v) {
            if ($k === 'user_email') {
                continue;
            }
            if (isset($_REQUEST[$k])) {
                $this->werte[$k] = $_REQUEST[$k];
            }
        }

        foreach ($this->fragenWerte as $k=>$v) {
            if (isset($_REQUEST[$k])) {
                $this->fragenWerte[$k] = $_REQUEST[$k];
            }
        }

        $this->werte['kenntnisnahme_datenverarbeitung'] = date('Y-m-d H:i:s');
        $this->werte['kenntnisnahme_datenverarbeitung_text'] = $this->smarty->fetch('datenschutz/kenntnisnahme_text.tpl');
        $this->werte['einwilligung_datenverarbeitung'] = date('Y-m-d H:i:s');
        $this->werte['einwilligung_datenverarbeitung_text'] = $this->smarty->fetch('datenschutz/einwilligung_text.tpl');

        $d = Daten::datenFromDbArray($this->werte);

        $a = new Antrag();
        $a->setStatus(Antrag::STATUS_BEWERTEN, 0);
        $a->setDaten($d);
        $a->setFragenWerte($this->fragenWerte);
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

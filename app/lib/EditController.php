<?php
namespace MHN\Aufnahme;

use MHN\Aufnahme\Antrag;
use MHN\Aufnahme\Service\Token;
use MHN\Aufnahme\Service\EmailService;
use MHN\Aufnahme\Domain\Model\FormData;
use MHN\Aufnahme\Domain\Repository\UserRepository;
use MHN\Aufnahme\Domain\Repository\TemplateRepository;

class EditController
{
    private $smarty = null;
    private $antrag = null;

    public function handleRequest($urlparams): void
    {
        $this->smarty = Service\SmartyContainer::getInstance()->getSmarty();

        try {
            preg_match('/^(\d+)/', $urlparams, $m);
            $this->antrag = new Antrag(intval($m[1]));
            $this->antrag->assertEditTokenValid($_GET['token']);
        } catch (\Exception $e) {
            $this->smarty->assign('innentemplate', 'EditController/tokenInvalid.tpl');
            return;
        }

        $this->smarty->assign('werte', $this->antrag->getDaten()->toArray());

        $action = $_POST['action'] ?? 'show form';

        if ($action === 'save') {
            $success = $this->handleActionSave();
            if (!$success) {
                $action = 'show form';
            } else {
                return;
            }
        }

        $this->handleActionShowForm();
    }

    private function handleActionSave(): bool
    {
        $daten = $this->antrag->getDaten();

        $dataIsValid = $daten->updateFromForm($this->smarty);

        if (!$dataIsValid) {
            $this->smarty->assign('werte', $daten->toArray());
            return false;
        }

        $this->antrag->setStatus(Antrag::STATUS_NEU_BEWERTEN, 0);
        $this->antrag->save();

        UserRepository::getInstance()->sendEmailToAll('Antrag bearbeitet', 'Ein Antrag wurde von der*dem Antragstellenden bearbeitet: ' . $this->antrag->getUrl());

        $this->smarty->assign('innentemplate', 'EditController/success.tpl');

        return true;
    }

    private function handleActionShowForm()
    {
        $this->smarty->assign('innentemplate', 'EditController/form.tpl');
    }
}

function EditController__handleRequest($parameter)
{
    $instance = new EditController();
    $instance->handleRequest($parameter['urlparams']);
}

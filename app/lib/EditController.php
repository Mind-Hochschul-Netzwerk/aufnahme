<?php
namespace MHN\Aufnahme;

use MHN\Aufnahme\Antrag;
use MHN\Aufnahme\Service\Token;
use MHN\Aufnahme\Service\EmailService;
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
        // TODO: Code zum Speichern eines Antrags nur an einer Stelle (aktuell in NeuController, lib_antraege und hier)

        $dataIsValid = true;

        $daten = $this->antrag->getDaten();

        // leere Checkboxen werden nicht gesendet
        foreach (formData::getSchema() as $key=>$type) {
            if ($type === 'bool') {
                $_POST[$key] = isset($_POST[$key]);
            }
        }

        foreach ($daten->getSchema() as $key=>$type) {
            // nicht im Formular änderbar:
            if (in_array($key, [
                'user_email',
                'kenntnisnahme_datenverarbeitung',
                'kenntnisnahme_datenverarbeitung_text',
                'einwilligung_datenverarbeitung',
                'einwilligung_datenverarbeitung_text'
            ], true)) {
                continue;
            }

            if (!isset($_POST[$key])) {
                die('nicht vom Formular gesetzt: ' . $key);
            }

            if ($key === 'mhn_geburtstag') {
                $birthday = formData::parseBirthdayInput($_POST[$key]);
                $daten->set($key, $birthday);
                if (!$birthday) {
                    $this->smarty->assign('invalidBirthday', true);
                    $dataIsValid = false;
                }
                continue;
            }

            $daten->set($key, $_POST[$key]);
        }

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

<?php
namespace MHN\Aufnahme;

use MHN\Aufnahme\Daten;
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

        $this->smarty->assign('werte', $this->antrag->daten->toArray());
        $this->smarty->assign('fragen', $GLOBALS['global_fragen']);
        $this->smarty->assign('fragen_werte', $this->antrag->getFragenWerte());

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

        $daten = Daten::datenByAntragId($this->antrag->getId());

        // leere Checkboxen werden nicht gesendet
        foreach (Daten::KEYS_CHECKBOX as $key) {
            $_POST[$key] = isset($_POST[$key]) ? 'j' : 'n';
        }

        foreach (Daten::KEYS as $key) {
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
            if (!property_exists($daten, $key)) {
                throw new \LogicException('Unbekannte Property in daten: ' . $key);
            }
            if (!isset($_POST[$key])) {
                throw new \RuntimeException('nicht vom Formular gesetzt: ' . $key);
            }
            if ($key === 'mhn_geburtstag') {
                $daten->$key = Daten::parseBirthdayInput($_POST[$key]);
                if (!($daten->$key)) {
                    $this->smarty->assign('invalidBirthday', true);
                    $dataIsValid = false;
                }
                continue;
            }
            $daten->$key = $_POST[$key];
        }

        $fragenWerte = $this->antrag->getFragenWerte();
        foreach ($fragenWerte as $k=>$v) {
            if (isset($_POST[$k])) {
                $fragenWerte[$k] = $_POST[$k];
            }
        }

        if (!$dataIsValid) {
            $this->smarty->assign('werte', $daten->toArray());
            $this->smarty->assign('fragen_werte', $fragenWerte);
            return false;
        }

        $daten->save();
        $this->antrag->setFragenWerte($fragenWerte);
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

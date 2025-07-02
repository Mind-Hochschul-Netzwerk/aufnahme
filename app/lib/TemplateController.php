<?php
namespace App;

use App\Domain\Repository\TemplateRepository;
use App\Service\SmartyContainer;

class TemplateController
{
    private $smarty = null;
    private $repository = null;

    public function handleRequest($templateName): void
    {
        $this->smarty = SmartyContainer::getInstance()->getSmarty();
        $this->repository = TemplateRepository::getInstance();

        if ($templateName) {
            $this->showForm($templateName);
            return;
        }

        if (!empty($_POST['templateName'])) {
            try {
                $this->save($_POST['templateName'], $_POST['subject'], $_POST['text']);
            } catch (\Exception $e) {
                die('invalid input');
            }
        }

        $this->showList();

    }

    private function showList(): void
    {
        $this->smarty->assign('templates', $this->repository->getAll());
        $this->smarty->assign('innentemplate', 'TemplateController/list.tpl');
    }

    private function showForm(string $templateName): void
    {
        try {
            $this->smarty->assign('template', $this->repository->getOneByName($templateName));
        } catch (\OutOfBoundsException $e) {
            die($e->getMessage());
        }
        $this->smarty->assign('innentemplate', 'TemplateController/form.tpl');
    }

    private function save(string $templateName, string $subject, string $text): void
    {
        $template = $this->repository->getOneByName($templateName);
        $template->setSubject($subject);
        $template->setText($text);
        $this->repository->save($template);
    }
}

function templateController__handleRequest($parameter)
{
    $instance = new TemplateController();
    $instance->handleRequest($parameter['urlparams']);
}

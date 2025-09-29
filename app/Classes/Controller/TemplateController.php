<?php
namespace App\Controller;

use App\Controller\Controller;
use App\Model\Template;
use App\Repository\TemplateRepository;
use Hengeb\Router\Attribute\Route;
use Hengeb\Router\Attribute\RequestValue;
use Hengeb\Router\Attribute\RequireLogin;
use Symfony\Component\HttpFoundation\Response;

class TemplateController extends Controller
{
    public function __construct(
        private TemplateRepository $repository,
    ) {}

    #[Route('GET /templates'), RequireLogin]
    public function showList(): Response
    {
        return $this->render('TemplateController/list', ['templates' => $this->repository->getAll()]);
    }

    #[Route('GET /templates/{name=>template}'), RequireLogin]
    public function showForm(Template $template): Response
    {
        return $this->render('TemplateController/form', ['template' => $template]);
    }

    #[Route('POST /templates/{name=>template}'), RequireLogin]
    public function save(Template $template, #[RequestValue] string $subject, #[RequestValue] string $text): Response
    {
        $template->setSubject(trim($subject));
        $template->setText(trim($text));
        $this->repository->save($template);
        return $this->redirect('/templates');
    }
}

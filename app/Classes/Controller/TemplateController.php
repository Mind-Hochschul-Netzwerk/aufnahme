<?php
namespace App\Controller;

use App\Controller\Controller;
use App\Model\Template;
use App\Repository\TemplateRepository;
use App\Service\CurrentUser;
use Hengeb\Router\Attribute\Route;
use Hengeb\Router\Attribute\RequestValue;
use Hengeb\Router\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TemplateController extends Controller
{

    public function __construct(
        protected Request $request,
        private CurrentUser $currentUser,
        private TemplateRepository $repository,
        Router $router,
    )
    {
        $router->addType(Template::class, fn(string $name) => $this->repository->getOneByName($name));
    }

    #[Route('GET /templates', ['loggedIn' => true])]
    public function showList(): Response
    {
        return $this->render('TemplateController/list', ['templates' => $this->repository->getAll()]);
    }

    #[Route('GET /templates/{name=>template}', ['loggedIn' => true])]
    public function showForm(Template $template): Response
    {
        return $this->render('TemplateController/form', ['template' => $template]);
    }

    #[Route('POST /templates/{name=>template}', ['loggedIn' => true])]
    public function save(Template $template, #[RequestValue] string $subject, #[RequestValue] string $text): Response
    {
        $template->setSubject(trim($subject));
        $template->setText(trim($text));
        $this->repository->save($template);
        return $this->redirect('/templates');
    }
}

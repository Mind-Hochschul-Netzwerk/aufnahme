<?php
namespace App\Controller;

use App\Repository\UserRepository;
use Hengeb\Router\Attribute\RequireLogin;
use Hengeb\Router\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    #[Route('GET /users'), RequireLogin]
    public function index(UserRepository $repository): Response
    {
        return $this->render('UserController/index', ['webusers' => $repository->findAll()]);
    }
}

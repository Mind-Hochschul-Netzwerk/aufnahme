<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use Hengeb\Db\Db;
use Hengeb\Router\Attribute\PublicAccess;
use Hengeb\Router\Attribute\RequestValue;
use Hengeb\Router\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller {
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    #[Route('GET /(login|)'), PublicAccess]
    public function loginForm(): Response {
        if ($this->currentUser->isLoggedIn()) {
            return $this->redirect('/antraege');
        }
        $redirect = $this->request->getPathInfo();
        return $this->render('AuthController/login', [
            'redirect' => $redirect,
            'login' => '',
            'password' => '',
        ]);
    }

    #[Route('POST /login'), PublicAccess]
    public function loginSubmitted(#[RequestValue] string $login, #[RequestValue] string $password, #[RequestValue] string $redirect): Response {
        if (!$login && !$password) {
            return $this->render('AuthController/login', [
                'redirect' => $redirect,
                'login' => '',
                'password' => '',
            ]);
        }

        $user = $this->userRepository->findOneByCredentials($login, $password);

        if (!$user) {
            return $this->render('AuthController/login', [
                'redirect' => $redirect,
                'login' => $login,
                'password' => '',
                'message' => 'Login fehlgeschlagen'
            ]);
        }

        $redirectUrl = preg_replace('/\s/', '', $redirect);

        $this->currentUser->logIn($user);
        return $this->redirect($redirectUrl);
    }

    #[Route('GET /logout'), PublicAccess]
    public function logout(): Response {
        $this->currentUser->logOut();
        return $this->redirect('/');
    }
}

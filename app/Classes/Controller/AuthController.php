<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\CurrentUser;
use Hengeb\Db\Db;
use Hengeb\Router\Attribute\RequestValue;
use Hengeb\Router\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller {
    #[Route('GET /(login|)', allow: true)]
    public function loginForm(): Response {
        if (CurrentUser::getInstance()->isLoggedIn()) {
            return $this->redirect('/antraege');
        }
        $redirect = $this->request->getPathInfo();
        return $this->render('AuthController/login', [
            'redirect' => $redirect,
            'login' => '',
            'password' => '',
        ]);
    }

    #[Route('POST /login', allow: true)]
    public function loginSubmitted(Db $db, CurrentUser $currentUser, #[RequestValue] string $login, #[RequestValue] string $password, #[RequestValue] string $redirect): Response {
        if (!$login && !$password) {
            return $this->render('AuthController/login', [
                'redirect' => $redirect,
                'login' => '',
                'password' => '',
            ]);
        }

        $user = UserRepository::getInstance()->findOneByCredentials($login, $password);

        if (!$user) {
            return $this->render('AuthController/login', [
                'redirect' => $redirect,
                'login' => $login,
                'password' => '',
                'message' => 'Login fehlgeschlagen'
            ]);
        }

        $redirectUrl = preg_replace('/\s/', '', $redirect);

        $currentUser->logIn($user);
        return $this->redirect($redirectUrl);
    }

    #[Route('GET /logout', allow: true)]
    public function logout(CurrentUser $user): Response {
        $user->logOut();
        return $this->redirect('/');
    }

    public static function handleNotLoggedInException(\Exception $e, Request $request): Response {
        return (new self($request))->loginForm();
    }
}

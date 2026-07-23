<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\OpenIdConnect;
use Hengeb\Router\Attribute\PublicAccess;
use Hengeb\Router\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller {
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    /**
     * Single route for both the OIDC login initiation and the callback (redirect_uri).
     * It is also invoked by Controller::handleException for unauthenticated HTML requests,
     * in which case the originally requested path is preserved as the post-login target.
     */
    #[Route('GET /login'), PublicAccess]
    public function login(OpenIDConnect $openIdConnect): Response {
        $session = $this->request->getSession();
        $isCallback = $this->request->query->has('code') || $this->request->query->has('error');

        if (!$isCallback) {
            if ($this->currentUser->isLoggedIn()) {
                return $this->redirect('/');
            }
            // remember where to send the user after a successful login
            $target = $this->request->getPathInfo() !== '/login'
                ? $this->request->getRequestUri()
                : ($this->request->query->getString('redirect') ?: '/');
            $session->set('oidc_redirect', $target);

            return $openIdConnect->authenticate();
        }

        // callback from the IdP: validate the tokens
        $openIdConnect->authenticate();

        $session->remove('oidc_stepup');

        if (!$this->currentUser->isLoggedIn()) {
            $username = $openIdConnect->username;
            $user = $username ? $this->userRepository->findOneByUsername($username) : null;
            if (!$user) {
                return $this->showError('Es wurde kein Mitgliedskonto zu diesem Login gefunden. Bitte wende dich an die Mitgliederverwaltung.', 403);
            }
            $this->currentUser->logIn($user);
        }

        $redirectUrl = $this->sanitizeLocalPath((string) $session->get('oidc_redirect', '/'));
        $session->remove('oidc_redirect');

        return $this->redirect($redirectUrl);
    }

    /**
     * Logs out of the app first, then routes the browser through Authelia's own logout page
     */
    #[Route('GET /logout'), PublicAccess]
    public function logout(): RedirectResponse {
        $this->currentUser->logOut();
        $returnUrl = 'https://mitglieder.' . getenv('DOMAINNAME') . '/logout?idpDone=1';
        return $this->redirect('https://sso.' . getenv('DOMAINNAME') . '/logout?rd=' . urlencode($returnUrl));
    }

    /**
     * Only allow local absolute paths as redirect targets to prevent open redirects.
     */
    private function sanitizeLocalPath(string $target): string {
        $target = preg_replace('/\s/', '', $target);
        if ($target === '' || $target[0] !== '/' || str_starts_with($target, '//')) {
            return '/';
        }
        return $target;
    }
}

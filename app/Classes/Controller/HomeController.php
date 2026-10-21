<?php
namespace App\Controller;

use Hengeb\Router\Attribute\PublicAccess;
use Hengeb\Router\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Weiterleitung von "/"
 */
class HomeController extends Controller
{
    #[Route('GET /'), PublicAccess]
    public function home(): RedirectResponse {
        if ($this->currentUser->hasGroup('aufnahme')) {
            return $this->redirect('/antraege');
        } elseif ($this->currentUser->hasGroup('aktivierung')) {
            return $this->redirect('/aktivierung');
        } else {
            return $this->redirect('/antrag');
        }
    }
}

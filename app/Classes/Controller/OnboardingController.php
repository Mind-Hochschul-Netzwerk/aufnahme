<?php
namespace App\Controller;

use App\Controller\Controller;
use App\Model\Antrag;
use App\Repository\AntragRepository;
use Hengeb\Router\Attribute\AllowIf;
use Hengeb\Router\Attribute\CheckCsrfToken;
use Hengeb\Router\Attribute\Route;
use Hengeb\Router\Exception\AccessDeniedException;
use Hengeb\Token\Token;
use Symfony\Component\HttpFoundation\JsonResponse;

class OnboardingController extends Controller
{
    public function __construct(
        private AntragRepository $antragRepository,
    ) {}

    #[Route('GET /onboarding/?token={token}'), AllowIf(backendConnection: true)]
    public function fetch(string $token): JsonResponse
    {
        $antrag = $this->getAntragByToken($token);
        return new JsonResponse(json_decode($antrag->getDaten()->json()));
    }

    #[Route('DELETE /onboarding/?token={token}'), AllowIf(backendConnection: true), CheckCsrfToken(false)]
    public function finish(string $token): JsonResponse
    {
        $antrag = $this->getAntragByToken($token);
        $antrag->setStatus(Antrag::STATUS_AKTIVIERT, "");
        $this->antragRepository->save($antrag);
        return new JsonResponse('success');
    }

    private function getAntragByToken(string $token): Antrag {
        try {
            $antragId = Token::decode($token, null, getenv('TOKEN_KEY'))[0];
            $antrag = $this->antragRepository->getOneById($antragId);
        } catch (\Exception $e) {
            throw new AccessDeniedException('token is invalid');
        }
        if ($antrag->getStatus() !== Antrag::STATUS_AUFGENOMMEN) {
            throw new AccessDeniedException('status is invalid');
        }
        return $antrag;
    }
}

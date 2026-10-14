<?php
namespace App\Controller;

use App\Model\Antrag;
use App\Repository\AntragRepository;
use Hengeb\Router\Attribute\AllowIf;
use Hengeb\Router\Attribute\Route;
use Hengeb\Router\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lesezugriff für die Gruppe "aktivierung" auf positiv beschiedene Anträge
 * (ohne Voten/Kommentare/Mailverlauf der Aufnahmekommission, ohne Speichermöglichkeit).
 */
class AktivierungController extends Controller
{
    public function __construct(
        private AntragRepository $repository,
    ) {}

    #[Route('GET /aktivierung'), AllowIf(group: 'aktivierung')]
    public function showAntraege(): Response
    {
        $antraege = [
            ...$this->repository->getAllByStatus(Antrag::STATUS_AUFGENOMMEN),
            ...$this->repository->getAllByStatus(Antrag::STATUS_AKTIVIERT),
        ];

        return $this->render('AktivierungController/liste', [
            'antraege' => $antraege,
        ]);
    }

    #[Route('GET /aktivierung/{id=>antrag}'), AllowIf(group: 'aktivierung')]
    public function showOne(Antrag $antrag): Response
    {
        if (!in_array($antrag->getStatus(), [Antrag::STATUS_AUFGENOMMEN, Antrag::STATUS_AKTIVIERT], true)) {
            throw new NotFoundException();
        }

        return $this->render('AktivierungController/antrag', [
            ...$antrag->getDaten()->toArray(),
            'antrag' => $antrag,
        ]);
    }
}

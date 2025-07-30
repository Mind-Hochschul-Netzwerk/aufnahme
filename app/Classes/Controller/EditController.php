<?php
namespace App\Controller;

use App\Model\Antrag;
use App\Model\FormData;
use App\Repository\AntragRepository;
use App\Repository\UserRepository;
use App\Service\CurrentUser;
use App\Service\Tpl;
use Hengeb\Router\Attribute\Route;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditController extends Controller
{
    public function __construct(
        protected Request $request,
        private CurrentUser $currentUser,
        private AntragRepository $repository,
    )
    {
    }

    #[Route('GET /edit/{id=>antrag}/?token={token}', allow: true)]
    public function form(Antrag $antrag, string $token): Response
    {
        if (!$antrag->validateEditToken($token)) {
            return $this->render('EditController/tokenInvalid');
        }

        return $this->render('EditController/form', [
            'antrag' => $antrag,
            'werte' => $antrag->getDaten()->toArray(),
        ]);
    }

    #[Route('POST /edit/{id=>antrag}/?token={token}', allow: true)]
    public function submit(Antrag $antrag, string $token, ParameterBag $submittedData): Response
    {
        if (!$antrag->validateEditToken($token)) {
            return $this->render('EditController/tokenInvalid');
        }

        $daten = $antrag->getDaten();

        $dataIsValid = $daten->updateFromForm($submittedData);

        $birthday = FormData::parseBirthdayInput($submittedData->get('mhn_geburtstag'));
        if ($birthday) {
            $daten->set('mhn_geburtstag', $birthday);
        } else {
            Tpl::getInstance()->set('invalidBirthday', true);
            $dataIsValid = false;
        }

        if (!$dataIsValid) {
            return $this->form($antrag, $token);
        }

        $antrag->setStatus(Antrag::STATUS_NEU_BEWERTEN, 0);
        $this->repository->save($antrag);

        UserRepository::getInstance()->sendEmailToAll('Antrag bearbeitet', "Ein Antrag wurde von der*dem Antragstellenden bearbeitet:\n" . $antrag->getUrl());

        return $this->render('EditController/success');
    }
}

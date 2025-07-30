<?php
namespace App\Controller;

use App\Model\Vote;
use App\Model\Antrag;
use App\Model\FormData;
use App\Repository\AntragRepository;
use App\Repository\EmailRepository;
use App\Repository\UserRepository;
use App\Repository\VoteRepository;
use App\Service\CurrentUser;
use App\Service\Tpl;
use App\Util;
use Hengeb\Router\Attribute\RequestValue;
use Hengeb\Router\Attribute\Route;
use Hengeb\Router\Exception\InvalidUserDataException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AntragController extends Controller
{
    public function __construct(
        protected Request $request,
        private CurrentUser $currentUser,
        private AntragRepository $repository,
    )
    {
    }

    #[Route('GET /antraege', ['loggedIn' => true])]
    public function showOffeneAntraege(): Response
    {
        $antraege = $this->repository->alleOffenenAntraege();
        return $this->showAntraege($antraege);
    }

    #[Route('GET /antraege/nichtvonmirgevotet/', ['loggedIn' => true])]
    public function showAntraegeNichtVonMirGevotet(): Response
    {
        $antraege = $this->repository->alleOffenenAntraege();

        foreach ($antraege as $k => $antrag) {
            $vote = $antrag->getLatestVoteByUserName($this->currentUser->getUserName());

            if ($vote === null) {
                continue;
            }

            // wenn Antragsstatus "neu bewerten" und seitdem nicht neu bewertet: auch behalten!
            if ($antrag->getStatus() == Antrag::STATUS_NEU_BEWERTEN && $vote->getTime()->getTimestamp() < $antrag->ts_statusaenderung) {
                continue;
            }

            unset($antraege[$k]);
        }

        Tpl::getInstance()->set('nichtvonmirgevotet', true);
        return $this->showAntraege($antraege);
    }

    private function showAntraege(array $antraege): Response {
        $userNames_gevotet = $this->getUserNamesGevotet($antraege);
        $realNames = $this->getRealNames($userNames_gevotet);
        return $this->render('AntragController/uebersicht', [
            'realNames' => $realNames,
            'userNames_gevotet' => $userNames_gevotet,
            'antraege' => $antraege,
        ]);
    }

    #[Route('GET /archiv', ['loggedIn' => true])]
    public function showEntschiedeneAntraege(): Response {
        $antraege = $this->repository->alleEntschiedenenAntraege();
        $userNames_gevotet = $this->getUserNamesGevotet($antraege);
        $realNames = $this->getRealNames($userNames_gevotet);
        return $this->render('AntragController/archiv', [
            'realNames' => $realNames,
            'userNames_gevotet' => $userNames_gevotet,
            'antraege' => $antraege,
        ]);
    }

    #[Route('GET /antraege/{id=>antrag}', ['loggedIn' => true])]
    public function showOne(Antrag $antrag): Response
    {
        $emails = EmailRepository::getInstance()->findAllByAntrag($antrag);
        $emailData = [];
        foreach ($emails as $email) {
            $user = UserRepository::getInstance()->findOneByUserName($email->getSenderUserName());
            $emailData[] = [
                'userName' => ($user !== null) ? $user->getUserName() : 'unbekannt',
                'time' => $email->getCreationTime(),
                'grund' => ucfirst($email->getGrund()),
            ];
        }

        return $this->render('AntragController/einzelansicht', [
            'mails' => $emailData,
            'antrag' => $antrag,
            'werte' => $antrag->getDaten()->toArray(),
            'heute' => Util::tsToDatum(time()),
            'statuscodes' => Antrag::STATUS_READABLE,
        ]);
    }

    #[Route('POST /antraege/{id=>antrag}', ['loggedIn' => true])]
    public function handleSubmit(Antrag $antrag, ParameterBag $submittedData, #[RequestValue] $formular): Response
    {
        return match ($formular) {
            'grunddaten' => $this->submitGrunddaten($antrag),
            'votes' => $this->submitVotes($antrag),
            'daten' => $this->submitDaten($antrag, $submittedData),
            default => throw new InvalidUserDataException('`formular` has invalid value'),
        };
    }

    #[Route('GET /antraege/{id=>antrag}/kommentare', ['loggedIn' => true])]
    public function showKommentare(Antrag $antrag): Response
    {
        return $this->render('AntragController/kommentare-editieren', [
            'antrag' => $antrag,
        ]);
    }

    #[Route('POST /antraege/{id=>antrag}/kommentare', ['loggedIn' => true])]
    public function storeKommentare(Antrag $antrag): Response
    {
        $input = $this->validatePayload([
            'kommentar' => 'string',
            'kommentare' => 'string',
        ]);

        if ($input['kommentar']) {
            $antrag->addKommentar($this->currentUser->getUserName(), $input['kommentar']);
            if ($this->repository->save($antrag)) {
                Tpl::getInstance()->set('meldung', 'Kommentar hinzugefügt');
            } else {
                Tpl::getInstance()->set('meldung', 'Fehler beim Hinzufügen des Kommentars');
            }
        } elseif ($input['kommentare']) {
            $antrag->setKommentare($input['kommentare']);
            if ($this->repository->save($antrag)) {
                Tpl::getInstance()->set('meldung', 'Kommentare geändert');
            } else {
                Tpl::getInstance()->set('meldung', 'Fehler beim Ändern von Kommentaren');
            }
        }

        return $this->showKommentare($antrag);
    }

    /**
     * Abschnitt 1: Grunddaten speichern
     */
    private function submitGrunddaten(Antrag $antrag): Response
    {
        $input = $this->validatePayload([
            'status' => 'int required',
            'datum_nachfrage' => 'string required',
            'datum_antwort' => 'string required',
            'datum_entscheidung' => 'string required',
            'bemerkung' => 'string required',
        ]);

        if (!isset(Antrag::STATUS_READABLE[$input['status']])) {
            throw InvalidUserDataException('Ungültiger Statuscode');
        }

        $antrag->setStatus($input['status'], $this->currentUser->getUserName());

        $antrag->setBemerkung($input['bemerkung']);
        $ts_nachfrage = Util::datumToTs($input['datum_nachfrage']);
        $ts_antwort = Util::datumToTs($input['datum_antwort']);
        $ts_entscheidung = Util::datumToTs($input['datum_entscheidung']);
        if ($ts_nachfrage === false || $ts_antwort === false || $ts_entscheidung === false) {
            Tpl::getInstance()->set('meldung', 'Fehler im Datum-Format.');
            return $this->showOne($antrag);
        }
        $antrag->setTsNachfrage($ts_nachfrage);
        $antrag->setTsAntwort($ts_antwort);
        $antrag->setTsEntscheidung($ts_entscheidung);
        $this->repository->save($antrag);

        return $this->redirect('/antraege/' . $antrag->getId());
    }

    /**
     * Abschnitt 2: Voten speichern
     *
     * Falls ein Votum abgegeben wurde, wird die Auführung beendet und zur Übersicht weitergeleitet.
     */
    private function submitVotes(Antrag $antrag): Response
    {
        $input = $this->validatePayload([
            'votum' => 'uint required',
            'nachfrage' => 'string required',
            'bemerkung' => 'string required',
        ]);

        if (!in_array($input['votum'], Vote::VALID_VALUES, true)) {
            throw new InvalidUserDataException('ungültiger Wert für `votum`');
        }

        Tpl::getInstance()->set('bemerkung', $input['bemerkung']);
        Tpl::getInstance()->set('nachfrage', $input['nachfrage']);

        if ($input['votum'] === Vote::NACHFRAGEN && !$input['nachfrage']) {
            Tpl::getInstance()->set('meldung', 'Fehler: "Nachfragen" gevotet, aber dort nichts eingetragen');
            return $this->showOne($antrag);
        } elseif ($input['votum'] === Vote::JA && $input['nachfrage']) {
            Tpl::getInstance()->set('meldung', 'Fehler: "Ja" gevotet, aber in "Nachfragen" etwas eingetragen');
            return $this->showOne($antrag);
        }

        $vote = new Vote();
        $vote->setUserName($this->currentUser->getUserName());
        $vote->setAntragId($antrag->getId());
        $vote->setValue($input['votum']);
        $vote->setNachfrage($input['nachfrage']);
        $vote->setBemerkung($input['bemerkung']);

        VoteRepository::getInstance()->add($vote);

        return $this->redirect('/antraege/');
    }

    /**
     * geänderte Daten speichern
     */
    private function submitDaten(Antrag $antrag, ParameterBag $submittedData): Response
    {
        $daten = $antrag->getDaten();
        $daten->updateFromForm($submittedData);
        $birthday = FormData::parseBirthdayInput($submittedData->get('mhn_geburtstag'));
        if ($birthday) {
            $daten->set('mhn_geburtstag', $birthday);
        }
        $this->repository->save($antrag);
        return $this->redirect('/antraege/' . $antrag->getId());
    }

    private function getUserNamesGevotet(array $antraege): array
    {
        $userNames_gevotet = [];
        foreach ($antraege as $antrag) {
            $votes = $antrag->getVotes();
            foreach ($votes as $vote) {
                $userName = $vote->getUserName();
                if (!in_array($userName, $userNames_gevotet, true)) {
                    $userNames_gevotet[] = $userName;
                }
            }
        }
        return $userNames_gevotet;
    }

    private function getRealNames(array $userNames_gevotet): array
    {
        $realNames = [];
        foreach ($userNames_gevotet as $userName) {
            $user = UserRepository::getInstance()->findOneByUserName($userName);
            if (!$user) {
                $realName[$userName] = '(unbekannt)';
            } else {
                $realNames[$userName] = $user->getRealName();
            }
        }
        return $realNames;
    }
}

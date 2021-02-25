<?php
namespace MHN\Aufnahme;

use MHN\Aufnahme\Domain\Repository\EmailRepository;
use MHN\Aufnahme\Domain\Repository\UserRepository;

function mails__laden($parameter)
{
    $smarty = Service\SmartyContainer::getInstance()->getSmarty();

    $matches = [];
    if (!preg_match('/^(\d+)\/(\d+)$/', $parameter['urlparams'], $matches)) {
        die('ungueltige URL');
    }
    $antrag_id = (int)$matches[1];
    $ts = (int)$matches[2];
    $smarty->assign('antrag_id', $antrag_id);
    $mails = EmailRepository::getInstance()->findByAntrag(new Antrag($antrag_id));
    foreach ($mails as $m) {
        if ($m->getCreationTime()->getTimestamp() === $ts) {
            $mail = $m;
            break;
        }
    }
    if (!isset($mail)) {
        $smarty->assign('fehler', 'Mail nicht gefunden');
    } else {
        $user = UserRepository::getInstance()->findOneByUserName($mail->getSenderUserName());
        $smarty->assign('mail', [
            'userName' => ($user !== null) ? $user->getRealName() : 'unbekannt',
            'time' => $mail->getCreationTime()->getTimestamp(),
            'grund' => ucfirst($mail->getGrund()),
            'subject' => $mail->getSubject(),
            'text' => $mail->getText(),
        ]);
    }
}

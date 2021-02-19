<?php
namespace MHN\Aufnahme;

// Composer
require_once '../vendor/autoload.php';

require_once('globals.php');
require_once('menue.php');

class Entrypoint
{
    /** @var string */
    const HOME_URI = '/antraege/';

    public function entry()
    {
        // Von Root-Seite auf Startseite weiterleiten
        if ($_SERVER['REQUEST_URI'] === '/') {
            header('Location: ' . self::HOME_URI);
            return;
        }

        Service\Session::getInstance()->start();

        menue_entry();
    }
}//class entrypoint

ob_start();
header('Content-Type: text/html; charset=utf-8');
Entrypoint::entry();
ob_end_flush();

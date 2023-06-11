<?php
namespace MHN\Aufnahme;

use Symfony\Component\Yaml\Yaml;

class Entrypoint
{
    const HOME_URI = '/antraege/';

    const MENU_FILENAME = '/var/www/Resources/Private/Menu.yml';
    const SITEMAP_FILENAME = '/var/www/Resources/Private/SiteMap.yml';

    public static function entry()
    {
        // Von Root-Seite auf Startseite weiterleiten
        if ($_SERVER['REQUEST_URI'] === '/') {
            header('Location: ' . static::HOME_URI);
            return;
        }

        ob_start();
        header('Content-Type: text/html; charset=utf-8');

        //Dateien, die erzeugt werden, sollen jeden Zugriff haben koennen:
        umask('0000');

        //Bei Assertions: anzeigen und Abbrechen (da viele Assetions auch als SIcherheitsueberprueung verwendet werden...)
        assert_options(ASSERT_ACTIVE, 1);
        assert_options(ASSERT_WARNING, 1);
        assert_options(ASSERT_BAIL, 1);

        include '../lib/wartung.php';

        Service\Session::getInstance()->start();

        // Beachte: der Unterschied zwischen 'offen' und 'entschieden' wird in antrag.php in alleOffenenAntraege und
        // alleEntschiedenenAntraege festgelegt!
        $GLOBALS['global_status'] = [
            Antrag::STATUS_NEU_BEWERTEN => 'Neu bewerten',
            Antrag::STATUS_BEWERTEN  => 'Bewerten',
            Antrag::STATUS_NACHFRAGEN  => 'Nachfragen',
            Antrag::STATUS_AUFNEHMEN => 'Aufnehmen',
            Antrag::STATUS_ABLEHNEN => 'Ablehnen',
            Antrag::STATUS_AUF_ANTWORT_WARTEN => 'Auf Antwort warten',
            Antrag::STATUS_AUFGENOMMEN => 'Aktivierungslink verschickt',
            Antrag::STATUS_AKTIVIERT => 'Mitgliedskonto aktiviert',
            Antrag::STATUS_ABGELEHNT => 'Abgelehnt',
        ];

        self::menue_entry();
    }

    /**
     * Lädt das Menü und die aufgerufene Seite
     */
    private static function menue_entry(): void
    {
        $smarty = Service\SmartyContainer::getInstance()->getSmarty();

        $loginGatekeeper = Service\LoginGatekeeper::getInstance();

        $loginGatekeeper->updateLoginStatus();

        $menuResource = Yaml::parse(file_get_contents(static::MENU_FILENAME));
        $siteMap = Yaml::parse(file_get_contents(static::SITEMAP_FILENAME));

        $activeEntry = null;
        $parameters = '';
        if (preg_match('/^\/(?P<page>[^\/]*)(\/(?P<parameters>.*))?$/', $_SERVER['REQUEST_URI'], $matches)) {
            $page = $matches['page'];
            if (!empty($matches['parameters'])) {
                $parameters = $matches['parameters'];
            }
            if (isset($siteMap[$page])) {
                $activeEntry = $siteMap[$page];
            }
        }
        $isUserLoggedIn = $loginGatekeeper->isUserLoggedIn();

        $menu = [];
        foreach ($menuResource as $uri => $item) {
            $siteMapEntry = $siteMap[$uri];

            if (!$isUserLoggedIn && empty($siteMapEntry['public'])) {
                continue;
            }

            $entry = [
                'link' => '/' . $uri . '/',
                'name' => $item['label'],
                'icon' => $item['icon'],
                'lib' => empty($siteMapEntry['lib']) ? null : $siteMapEntry['lib'],
                'title' => $siteMapEntry['title'],
                'aktiv' => false,
            ];

            if ($page === $uri) {
                $entry['aktiv'] = true;
            }

            $menu[] = $entry;
        }

        $smarty->assign('Menue', $menu);
        $smarty->assign('menue_vorhanden', count($menu) > 0);
        $smarty->assign('NVleiste', [$activeEntry]);
        $smarty->assign('html_title', $activeEntry['title'] ?? '');

        $isEmbedded = !empty($_GET['embed']);
        if ($isEmbedded) {
            $smarty->assign('isEmbedded', true);
            $parentUrl = empty($_GET['parentUrl']) ? '' : filter_var($_GET['parentUrl'], FILTER_VALIDATE_URL);
            $urlComponents = $parentUrl ? parse_url($parentUrl) : [];
            if (!empty($urlComponents['query'])) {
                parse_str($urlComponents['query'], $parentQuery);
                foreach ($parentQuery as $k=>$v) {
                    if (!isset($_GET[$k])) {
                        $_GET[$k] = $v;
                    }
                }
            }
        }

        if ($activeEntry === null) {
            @header('HTTP/1.1 404 File Not Found');
            $smarty->assign('innentemplate', '404.tpl');
            $smarty->display('main.tpl', '404.tpl');
            return;
        }

        if (empty($activeEntry['public'])) {
            $loginGatekeeper->protectThisPage();
        }

        if (isset($activeEntry['template'])) {
            $smarty->assign('innentemplate', $activeEntry['template']);
        }

        //evtl. lib-Datei importieren:
        if (isset($activeEntry['ladenfunktion'])) {
            require_once '../lib/' . $activeEntry['lib'];

            if (empty($activeEntry['ladenparameter'])) {
                $activeEntry['ladenparameter'] = [];
            }
            $activeEntry['ladenparameter']['urlparams'] = $parameters;
            $activeEntry['ladenfunktion'] = '\\MHN\\Aufnahme\\' . $activeEntry['ladenfunktion'];
            $activeEntry['ladenfunktion']($activeEntry['ladenparameter']);

            //Das auch den anderen mitteilen...
            @header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            @header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            @header('Cache-Control: no-store, no-cache, must-revalidate');
            @header('Cache-Control: post-check=0, pre-check=0', false);
            //HTTP1.0:
            @header('Pragma: no-cache');

            $smarty->display('main.tpl');
        } else {
            //als ID das innentemplate verwenden; die Seite ist damit eindeutig bestimmt
            $smarty->display('main.tpl', $activeEntry['template']);
        }
    }
}

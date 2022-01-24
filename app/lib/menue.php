<?php
namespace MHN\Aufnahme;

/**
 * @author Jochen Ott
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 */

use Symfony\Component\Yaml\Yaml;

const MENU_FILENAME = '/var/www/Resources/Private/Menu.yml';
const SITEMAP_FILENAME = '/var/www/Resources/Private/SiteMap.yml';

/**
 * Lädt das Menü und die aufgerufene Seite
 *
 * @return void
 */
function menue_entry()
{
    $smarty = Service\SmartyContainer::getInstance()->getSmarty();
    $loginGatekeeper = Service\LoginGatekeeper::getInstance();

    $loginGatekeeper->updateLoginStatus();

    $menuResource = Yaml::parse(file_get_contents(MENU_FILENAME));
    $siteMap = Yaml::parse(file_get_contents(SITEMAP_FILENAME));

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
        require_once $activeEntry['lib'];

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

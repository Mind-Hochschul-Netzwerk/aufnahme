<?php
namespace App\Service;

/**
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

use Smarty;

/**
 * Hält Smarty bereit
 */
class SmartyContainer implements \App\Interfaces\Singleton
{
    use \App\Traits\Singleton;

    /** @var Smarty */
    private $smarty = null;

    /**
     * Instanziierung nur über SmartyContainer::getInstance() (aus Interfaces\Singleton)
     */
    private function __construct()
    {
        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir(__DIR__ . '/../../Resources/Private/Templates/');
        $this->smarty->setCompileDir('/tmp/templates_c/');
        $this->smarty->setCacheDir('/tmp/cache/');
        $this->smarty->compile_check = true;

        $this->smarty->assign('DOMAINNAME', getenv('DOMAINNAME'));
        $this->smarty->assign('self', $_SERVER['REQUEST_URI']);
    }

    /**
     * Gibt das Smarty-Objekt zurück.
     *
     * @return Smarty
     */
    public function getSmarty()
    {
        return $this->smarty;
    }
}

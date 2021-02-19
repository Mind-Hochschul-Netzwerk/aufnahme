<?php
namespace MHN\Aufnahme\Tests\Unit\Service;

/**
 * @author Oliver Klee <mensa@oliverklee.de>
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */

use MHN\Aufnahme\Service\SmartyContainer;

/**
 * Testcase.
 */
class SmartyContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @var SmartyContainer */
    private $subject = null;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->subject = SmartyContainer::getInstance();
    }

    /**
     * @test
     */
    public function classIsSingleton()
    {
        self::assertSame(SmartyContainer::getInstance(), SmartyContainer::getInstance());
    }

    /**
     * @test
     */
    public function getSmartyReturnsSmartyInstance()
    {
        self::assertInstanceOf(\Smarty::class, $this->subject->getSmarty());
    }

    /**
     * @test
     */
    public function getSmartyCalledTwoTimesReturnsSameInstance()
    {
        self::assertSame($this->subject->getSmarty(), $this->subject->getSmarty());
    }

    /**
     * @test
     */
    public function activatesCompileCheck()
    {
        $smarty = $this->subject->getSmarty();

        self::assertTrue($smarty->compile_check);
    }

    /**
     * @test
     */
    public function setsTemplateDirectoryToTemplateResourcesDirectory()
    {
        $smarty = $this->subject->getSmarty();

        self::assertSame(
            [realpath(__DIR__ . '/../../../Resources/Private/Templates') . '/'],
            $smarty->getTemplateDir()
        );
    }

    /**
     * @test
     */
    public function setsCompileDirectoryToWithinTmp()
    {
        $smarty = $this->subject->getSmarty();

        self::assertSame(
            '/tmp/templates_c/',
            $smarty->getCompileDir()
        );
    }

    /**
     * @test
     */
    public function setsCacheDirectoryToWithinTmp()
    {
        $smarty = $this->subject->getSmarty();

        self::assertSame(
            '/tmp/cache/',
            $smarty->getCacheDir()
        );
    }
}

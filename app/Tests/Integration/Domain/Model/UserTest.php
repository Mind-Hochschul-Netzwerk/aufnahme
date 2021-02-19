<?php
namespace MHN\Aufnahme\Tests\Integration\Domain\Model;

/**
 * @author Oliver Klee <mensa@oliverklee.de>
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */

use MHN\Aufnahme\Domain\Model\User;

/**
 * Testcase.
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    /** @var User */
    private $subject = null;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->subject = new User();
    }

    /**
     * @test
     */
    public function setPasswordCreatesPbkdf2PasswordHash()
    {
        $this->subject->setPassword('My secret password!');

        self::assertRegExp('/^:pbkdf2:sha256:10000:128:[^:]{24}:[^:]{172}$/', $this->subject->getPasswordHash());
    }

    /**
     * @test
     */
    public function setPasswordTwoTimesWithSamePasswordCreatesDifferentHashes()
    {
        $password = 'inLucvic^OjIn2';

        $this->subject->setPassword($password);
        $hash1 = $this->subject->getPasswordHash();
        $this->subject->setPassword($password);
        $hash2 = $this->subject->getPasswordHash();

        self::assertNotSame($hash1, $hash2);
    }
}

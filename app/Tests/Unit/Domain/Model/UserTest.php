<?php
namespace MHN\Aufnahme\Tests\Unit\Domain\Model;

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
    public function getIdInitiallyReturnsZero()
    {
        self::assertSame(0, $this->subject->getId());
    }

    /**
     * @test
     */
    public function setIdSetsId()
    {
        $value = 123456;
        $this->subject->setId($value);

        self::assertSame($value, $this->subject->getId());
    }

    /**
     * @test
     */
    public function getUserNameInitiallyReturnsEmptyString()
    {
        self::assertSame('', $this->subject->getUserName());
    }

    /**
     * @test
     */
    public function setUserNameSetsUserName()
    {
        $value = 'Club-Mate';
        $this->subject->setUserName($value);

        self::assertSame($value, $this->subject->getUserName());
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     */
    public function setUserNameWithInvalidUserNameThrowsException()
    {
        $this->subject->setUserName('Schnösel = Brösel?');
    }

    /**
     * @return string[][]
     */
    public function validUserNameDataProvider()
    {
        return [
            'lowercase' => ['buzcqwetkjhl'],
            'uppercase' => ['JZTVTGL'],
            'numbers' => ['32514734253245'],
            'special characters' => ['._-'],
            'three letters' => ['abc'],
            'all character classes' => ['azAZ019._-'],
            'all character classes reversed' => ['-_.910ZAza'],
        ];
    }

    /**
     * @test
     * @param string $userName
     * @dataProvider validUserNameDataProvider
     */
    public function isUserNameValidWithValidUserNameReturnsTrue($userName)
    {
        self::assertTrue(User::isUserNameValid($userName));
    }

    /**
     * @return string[][]
     */
    public function invalidUserNameDataProvider()
    {
        return [
            'empty' => [''],
            '1 letter' => ['a'],
            '2 letters' => ['ab'],
            'umlauts' => ['Schöne-Grüße-an-das-Fräulein'],
            'spaces' => ['   '],
            'punctuation' => ['!?;:,'],
            'other special characters' => ['%$/&#*+'],
        ];
    }

    /**
     * @test
     * @param string $userName
     * @dataProvider invalidUserNameDataProvider
     */
    public function isUserNameValidWithInvalidUserNameReturnsFalse($userName)
    {
        self::assertFalse(User::isUserNameValid($userName));
    }

    /**
     * @test
     */
    public function getPasswordHashInitiallyReturnsEmptyString()
    {
        self::assertSame('', $this->subject->getPasswordHash());
    }

    /**
     * @test
     */
    public function setPasswordHashSetsPasswordHash()
    {
        $value = 'Club-Mate';
        $this->subject->setPasswordHash($value);

        self::assertSame($value, $this->subject->getPasswordHash());
    }

    /**
     * @test
     */
    public function getRealNameInitiallyReturnsEmptyString()
    {
        self::assertSame('', $this->subject->getRealName());
    }

    /**
     * @test
     */
    public function setRealNameSetsRealName()
    {
        $value = 'Club-Mate';
        $this->subject->setRealName($value);

        self::assertSame($value, $this->subject->getRealName());
    }
}

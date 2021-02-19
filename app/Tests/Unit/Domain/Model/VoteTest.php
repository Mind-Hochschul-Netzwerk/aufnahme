<?php
namespace MHN\Aufnahme\Tests\Unit\Domain\Model;

/**
 * @author Oliver Klee <mensa@oliverklee.de>
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */

use MHN\Aufnahme\Domain\Model\User;
use MHN\Aufnahme\Domain\Model\Vote;
use MHN\Aufnahme\Domain\Repository\UserRepository;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Testcase.
 */
class VoteTest extends \PHPUnit_Framework_TestCase
{
    /** @var Vote */
    private $subject = null;

    /** @var UserRepository|ObjectProphecy */
    private $userRepositoryProphecy = null;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->subject = new Vote();
        $this->userRepositoryProphecy = $this->prophesize(UserRepository::class);
        $this->injectUserRepositoryIntoSubject();
    }

    /**
     * @return void
     */
    private function injectUserRepositoryIntoSubject()
    {
        $reflectionObject = new \ReflectionObject($this->subject);
        $reflectionProperty = $reflectionObject->getProperty('userRepository');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->subject, $this->userRepositoryProphecy->reveal());
    }

    /**
     * @test
     */
    public function validValuesAreComplete()
    {
        self::assertSame(
            [Vote::NEIN, Vote::JA, Vote::NACHFRAGEN, Vote::ENTHALTUNG],
            Vote::VALID_VALUES
        );
    }

    /**
     * @test
     */
    public function getAntragIdInitiallyReturnsZero()
    {
        self::assertSame(0, $this->subject->getAntragId());
    }

    /**
     * @test
     */
    public function setAntragIdSetsAntragId()
    {
        $value = 123456;
        $this->subject->setAntragId($value);

        self::assertSame($value, $this->subject->getAntragId());
    }

    /**
     * @test
     */
    public function getUserIdInitiallyReturnsZero()
    {
        self::assertSame(0, $this->subject->getUserId());
    }

    /**
     * @test
     */
    public function setUserIdSetsUserId()
    {
        $value = 123456;
        $this->subject->setUserId($value);

        self::assertSame($value, $this->subject->getUserId());
    }

    /**
     * @test
     */
    public function getUserForNoUserSetReturnsNull()
    {
        $this->userRepositoryProphecy->findOneById(0)->willReturn(null)->shouldBeCalled();

        self::assertNull($this->subject->getUser());
    }

    /**
     * @test
     */
    public function getUserForInexistentUserIdReturnsNull()
    {
        $userId = 42;
        $this->subject->setUserId($userId);

        $this->userRepositoryProphecy->findOneById($userId)->willReturn(null)->shouldBeCalled();

        self::assertNull($this->subject->getUser());
    }

    /**
     * @test
     */
    public function getUserForExistingUserIdReturnsUser()
    {
        $userId = 42;
        $this->subject->setUserId($userId);
        $user = new User();
        $user->setId($userId);

        $this->userRepositoryProphecy->findOneById($userId)->willReturn($user)->shouldBeCalled();

        self::assertSame($user, $this->subject->getUser());
    }

    /**
     * @test
     */
    public function getUserNameForNoUserSetReturnsUnknownLabel()
    {
        $this->userRepositoryProphecy->findOneById(0)->willReturn(null)->shouldBeCalled();

        self::assertSame('unbekannt', $this->subject->getUserName());
    }

    /**
     * @test
     */
    public function getUserNameForInexistentUserReturnsUnknownLabel()
    {
        $userId = 42;
        $this->subject->setUserId($userId);

        $this->userRepositoryProphecy->findOneById($userId)->willReturn(null)->shouldBeCalled();

        self::assertSame('unbekannt', $this->subject->getUserName());
    }

    /**
     * @test
     */
    public function getUserNameForExistingUserReturnsUserName()
    {
        $userId = 42;
        $userName = 'hans-wurst';
        $this->subject->setUserId($userId);
        $user = new User();
        $user->setId($userId);
        $user->setUserName($userName);

        $this->userRepositoryProphecy->findOneById($userId)->willReturn($user)->shouldBeCalled();

        self::assertSame($userName, $this->subject->getUserName());
    }

    /**
     * @test
     */
    public function getTimeInitiallyReturnsDateTime()
    {
        self::assertInstanceOf(\DateTime::class, $this->subject->getTime());
    }

    /**
     * @test
     */
    public function setTimeSetsTime()
    {
        $Time = new \DateTime();
        $this->subject->setTime($Time);

        self::assertSame($Time, $this->subject->getTime());
    }

    /**
     * @test
     */
    public function getValueInitiallyReturnsZero()
    {
        self::assertSame(0, $this->subject->getValue());
    }

    /**
     * @test
     */
    public function setValueSetsValue()
    {
        $value = Vote::JA;
        $this->subject->setValue($value);

        self::assertSame($value, $this->subject->getValue());
    }

    /**
     * @return mixed[][]
     */
    public function valueLabelsDataProvider()
    {
        return [
            'no' => [Vote::NEIN, 'N'],
            'yes' => [Vote::JA, 'J'],
            'query' => [Vote::NACHFRAGEN, '?'],
            'abstain' => [Vote::ENTHALTUNG, '-'],
        ];
    }

    /**
     * @test
     * @param string $value
     * @param string $label
     * @dataProvider valueLabelsDataProvider
     */
    public function getValueReadableReturnsReadableLabels($value, $label)
    {
        $this->subject->setValue($value);

        self::assertSame($label, $this->subject->getValueReadable());
    }

    /**
     * @return mixed[][]
     */
    public function valueColorsDataProvider()
    {
        return [
            'no' => [Vote::NEIN, 'antrag_bewertung_rot'],
            'yes' => [Vote::JA, 'antrag_bewertung_gruen'],
            'query' => [Vote::NACHFRAGEN, 'antrag_bewertung_gelb'],
            'abstain' => [Vote::ENTHALTUNG, 'antrag_bewertung_weiss'],
        ];
    }

    /**
     * @test
     * @param string $value
     * @param string $cssClass
     * @dataProvider valueColorsDataProvider
     */
    public function getValueColorReturnsCssClasses($value, $cssClass)
    {
        $this->subject->setValue($value);

        self::assertSame($cssClass, $this->subject->getValueColor());
    }

    /**
     * @test
     */
    public function getBemerkungInitiallyReturnsEmptyString()
    {
        self::assertSame('', $this->subject->getBemerkung());
    }

    /**
     * @test
     */
    public function setBemerkungSetsBemerkung()
    {
        $value = 'Club-Mate';
        $this->subject->setBemerkung($value);

        self::assertSame($value, $this->subject->getBemerkung());
    }

    /**
     * @test
     */
    public function getNachfrageInitiallyReturnsEmptyString()
    {
        self::assertSame('', $this->subject->getNachfrage());
    }

    /**
     * @test
     */
    public function setNachfrageSetsNachfrage()
    {
        $value = 'Club-Mate';
        $this->subject->setNachfrage($value);

        self::assertSame($value, $this->subject->getNachfrage());
    }
}

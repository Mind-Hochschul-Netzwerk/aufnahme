<?php
namespace MHN\Aufnahme\Tests\Unit\Domain\Model;

/**
 * @author Oliver Klee <mensa@oliverklee.de>
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */

use MHN\Aufnahme\Domain\Model\Email;

/**
 * Testcase.
 */
class EmailTest extends \PHPUnit_Framework_TestCase
{
    /** @var Email */
    private $subject = null;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->subject = new Email();
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
    public function getGrundInitiallyReturnsEmptyString()
    {
        self::assertSame('', $this->subject->getGrund());
    }

    /**
     * @test
     */
    public function setGrundSetsGrund()
    {
        $value = 'Club-Mate';
        $this->subject->setGrund($value);

        self::assertSame($value, $this->subject->getGrund());
    }

    /**
     * @test
     */
    public function getSenderUserIdInitiallyReturnsZero()
    {
        self::assertSame(0, $this->subject->getSenderUserId());
    }

    /**
     * @test
     */
    public function setSenderUserIdSetsSenderUserId()
    {
        $value = 123456;
        $this->subject->setSenderUserId($value);

        self::assertSame($value, $this->subject->getSenderUserId());
    }

    /**
     * @test
     */
    public function getCreationTimeInitiallyReturnsDateTime()
    {
        self::assertInstanceOf(\DateTime::class, $this->subject->getCreationTime());
    }

    /**
     * @test
     */
    public function setCreationTimeSetsCreationTime()
    {
        $creationTime = new \DateTime();
        $this->subject->setCreationTime($creationTime);

        self::assertSame($creationTime, $this->subject->getCreationTime());
    }

    /**
     * @test
     */
    public function getSubjectInitiallyReturnsEmptyString()
    {
        self::assertSame('', $this->subject->getSubject());
    }

    /**
     * @test
     */
    public function setSubjectSetsSubject()
    {
        $value = 'Club-Mate';
        $this->subject->setSubject($value);

        self::assertSame($value, $this->subject->getSubject());
    }

    /**
     * @test
     */
    public function getTextInitiallyReturnsEmptyString()
    {
        self::assertSame('', $this->subject->getText());
    }

    /**
     * @test
     */
    public function setTextSetsText()
    {
        $value = 'Club-Mate';
        $this->subject->setText($value);

        self::assertSame($value, $this->subject->getText());
    }
}

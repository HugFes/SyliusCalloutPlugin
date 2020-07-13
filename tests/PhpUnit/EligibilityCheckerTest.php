<?php


namespace Tests\Setono\SyliusCalloutPlugin\PhpUnit;

use PHPUnit\Framework\TestCase;
use Setono\SyliusCalloutPlugin\Callout\Checker\RenderingEligibility\CalloutDurationEligibilityChecker;
use Setono\SyliusCalloutPlugin\Model\Callout;
use Setono\SyliusCalloutPlugin\Model\CalloutInterface;

class EligibilityCheckerTest extends TestCase
{

    /**
     * @var CalloutDurationEligibilityChecker
     */
    private $CalloutDurationEligibilityChecker;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->CalloutDurationEligibilityChecker = new CalloutDurationEligibilityChecker();
    }

    /**
     * @test
     * @dataProvider isDurationEligibleDataProvider
     * @param  $callout
     */
    public function isDurationEligible(CalloutInterface $callout){
       $this->assertTrue(
            $this->CalloutDurationEligibilityChecker->isEligible($callout),
           sprintf(
               "The callout is not eligible into current duration: %s -> %s",
               $callout->getStartsAt() ? $callout->getStartsAt()->format("Y-m-d") : 'Undefined',
               $callout->getEndsAt() ? $callout->getEndsAt()->format("Y-m-d") : 'Undefined'
           )
        );
    }

    /**
     * @return array
     */
    public function isDurationEligibleDataProvider(): array
    {
        $data = [];

        //without start date and end date
        $calloutWithoutDate= new Callout();
        $calloutWithoutDate->setStartsAt(null);
        $calloutWithoutDate->setEndsAt(null);
        $data[] = [$calloutWithoutDate];

        //with only start date (yesterday)
        $calloutWithStartDate = new Callout();
        $calloutWithStartDate->setStartsAt( (new \DateTime())->sub(new \DateInterval('P1D')));
        $calloutWithStartDate->setEndsAt(null);
        $data[] = [$calloutWithStartDate];

        //with only end date (tomorrow)
        $calloutWithEndDate = new Callout();
        $calloutWithEndDate->setStartsAt(null);
        $calloutWithEndDate->setEndsAt((new \DateTime())->add(new \DateInterval('P1D')));
        $data[] = [$calloutWithEndDate];

        //with start and end date
        $calloutWithStartAndEndDate = new Callout();
        $calloutWithStartAndEndDate->setStartsAt((new \DateTime())->sub(new \DateInterval('P1D')));
        $calloutWithStartAndEndDate->setEndsAt((new \DateTime())->add(new \DateInterval('P1D')));
        $data[] = [$calloutWithStartAndEndDate];

        return $data;
    }

    /**
     * @test
     * @dataProvider isNotDurationEligibleDataProvider
     * @param  $callout
     */
    public function isNotDurationEligible(CalloutInterface $callout){
        $this->assertFalse(
            $this->CalloutDurationEligibilityChecker->isEligible($callout),
            sprintf(
                "The callout is eligible into current duration: %s -> %s",
                $callout->getStartsAt() ? $callout->getStartsAt()->format("Y-m-d") : 'Undefined',
                $callout->getEndsAt() ? $callout->getEndsAt()->format("Y-m-d") : 'Undefined'
            )
        );
    }

    /**
     * @return array
     */
    public function isNotDurationEligibleDataProvider(): array
    {
        $data = [];

        //with only start date (tomorrow)
        $calloutWithStartDate = new Callout();
        $calloutWithStartDate->setStartsAt( (new \DateTime())->add(new \DateInterval('P1D')));
        $calloutWithStartDate->setEndsAt(null);
        $data[] = [$calloutWithStartDate];

        //with only end date (yesterday)
        $calloutWithEndDate = new Callout();
        $calloutWithEndDate->setStartsAt(null);
        $calloutWithEndDate->setEndsAt((new \DateTime())->sub(new \DateInterval('P1D')));
        $data[] = [$calloutWithEndDate];

        //with start and end date (before now)
        $calloutWithStartAndEndDate = new Callout();
        $calloutWithStartAndEndDate->setStartsAt((new \DateTime())->sub(new \DateInterval('P2D')));
        $calloutWithStartAndEndDate->setEndsAt((new \DateTime())->sub(new \DateInterval('P1D')));
        $data[] = [$calloutWithStartAndEndDate];

        //with start and end date (after now)
        $calloutWithStartAndEndDateTomorrow = new Callout();
        $calloutWithStartAndEndDate->setStartsAt((new \DateTime())->add(new \DateInterval('P1D')));
        $calloutWithStartAndEndDate->setEndsAt((new \DateTime())->add(new \DateInterval('P2D')));
        $data[] = [$calloutWithStartAndEndDate];

        return $data;
    }
}

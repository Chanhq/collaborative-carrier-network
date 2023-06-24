<?php

namespace Tests\Unit\BusinessDomain\RevenueCalculation;

use App\BusinessDomain\RevenueCalculation\Service\TransportPriceCalculationService;
use App\Infrastructure\Map\DistanceCalculation\DistanceCalculatorInterface;
use App\Models\TransportRequest;
use App\Models\User;
use Mockery\MockInterface;
use Tests\TestCase;

class TransportPriceCalculationServiceTest extends TestCase
{
    public function testCalculatesThePriceForOneTransportRequest(): void
    {
        /** @var User $user */
        $user = User::factory(1)->create()->first();
        $transportRequest = $this->mock(TransportRequest::class, function (MockInterface $mock) {
            $mock->expects('originNode')->andReturn(1);
            $mock->expects('destinationNode')->andReturn(1);
        });
        $this->assertEquals(
            40,
            $this->getUnitUnderTest(1)
                ->calculatePriceForTransportRequest($transportRequest, $user)
        );
    }

    public function testCalculatesThePriceForATransportRequestSet(): void
    {
        /** @var User $user */
        $user = User::factory(1)->create()->first();

        $transportRequest = $this->mock(TransportRequest::class, function (MockInterface $mock) {
            $mock->expects('originNode')->times(3)->andReturn(1);
            $mock->expects('destinationNode')->times(3)->andReturn(1);
        });

        $transportRequestSet = [];
        $transportRequestSet[] = $transportRequest;
        $transportRequestSet[] = $transportRequest;
        $transportRequestSet[] = $transportRequest;
        $numberOfTransportRequests = \count($transportRequestSet);

        $this->assertEquals(
            40 * $numberOfTransportRequests,
            $this->getUnitUnderTest($numberOfTransportRequests)
                ->calculatePriceForTransportRequestSet($transportRequestSet, $user)
        );
    }

    private function getUnitUnderTest(int $numberOfConcernedTransportRequests): TransportPriceCalculationService
    {
        $distanceCalculator = $this->mock(
            DistanceCalculatorInterface::class,
            function (MockInterface $mock) use ($numberOfConcernedTransportRequests) {
                $mock->expects('calculateDistance')
                    ->times($numberOfConcernedTransportRequests)
                    ->andReturn(10);
            }
        );

        return new TransportPriceCalculationService($distanceCalculator);
    }
}

<?php

namespace Tests\Unit\BusinessDomain\RevenueCalculation;

use App\BusinessDomain\RevenueCalculation\Service\TransportCostCalculationService;
use App\BusinessDomain\VehicleRouting\DTO\Edge;
use App\Infrastructure\Map\DistanceCalculation\DistanceCalculatorInterface;
use App\Models\User;
use Mockery\MockInterface;
use Tests\TestCase;

class TransportCostCalculationServiceTest extends TestCase
{
    /**
     * @dataProvider pathsDataProvider
     *
     * @param Edge[] $optimalPathWithTransportRequest
     * @param Edge[] $optimalPathWithoutTransportRequest
     * @param int $expectedCostOfTransportRequest
     */
    public function testCalculatesCostOfASingleTransportRequest(
        array $optimalPathWithTransportRequest,
        array $optimalPathWithoutTransportRequest,
        int $expectedCostOfTransportRequest
    ): void {
        /** @var User $user */
        $user = User::factory(1)->create()->first();
        $this->assertEquals(
            $expectedCostOfTransportRequest,
            $this->getUnitUnderTest()->calculateTransportRequestCost(
                $optimalPathWithTransportRequest,
                $optimalPathWithoutTransportRequest,
                $user,
            )
        );
    }

    /**
     * @dataProvider pathDataProvider
     * @param Edge[] $path
     */
    public function testCalculatesCostForAPath(array $path, int $expectedCostOfPath)
    {
        /** @var User $user */
        $user = User::factory(1)->create()->first();
        self::assertEquals($expectedCostOfPath, $this->getUnitUnderTest()->calculateTotalCostOfPath($path, $user));
    }

    /**
     * @return array<array<Edge[], int>>
     */
    public static function pathDataProvider(): array
    {
        return [
            'path' => [
                [
                    new Edge(1, 12, 1, 2),
                    new Edge(2, 7, 2, 3),
                    new Edge(3, 40, 3, 7),
                    new Edge(4, 21, 7, 12),
                    new Edge(5, 31, 12, 1),
                ],
                161
            ]
        ];
    }

    /**
     * @return array<array<Edge[], Edge[], int>>
     */
    public static function pathsDataProvider(): array
    {
        return [
            'first path' => [
                [
                    new Edge(1, 12, 1, 2),
                    new Edge(2, 7, 2, 3),
                    new Edge(3, 40, 3, 7),
                    new Edge(4, 21, 7, 12),
                    new Edge(5, 31, 12, 1),
                ],
                [
                    new Edge(1, 12, 1, 2),
                    new Edge(2, 7, 2, 3),
                    new Edge(6, 21, 3, 4),
                    new Edge(7, 11, 4, 7),
                    new Edge(8, 31, 7, 1),
                ],
                39
            ]
        ];
    }

    private function getUnitUnderTest(): TransportCostCalculationService
    {
        return new TransportCostCalculationService();
    }
}

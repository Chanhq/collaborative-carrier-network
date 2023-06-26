<?php

namespace Tests\Integration;

use App\BusinessDomain\VehicleRouting\PythonVehicleRoutingWrapper;
use App\Models\TransportRequest;
use App\Models\User;
use Tests\TestCase;

class PythonVehicleRoutingWrapperTest extends TestCase
{
    public function testDummy(): void
    {
        self::assertTrue(true);
    }

    // TODO: Make it work in CI, python dependency this is why it has no test prefix
    /**
     * @throws \JsonException
     */
    public function itCalculatesTheOptimalPath(): void
    {
        /** @var User $user */
        $user = User::factory(1)->create()->first();
        $user->transportRequests()->save(new TransportRequest([
            'origin_node' => 2,
            'destination_node' => 3,
        ]));

        $transportRequests = [];
        /** @var TransportRequest $transportRequest */
        foreach ($user->transportRequests()->get() as $transportRequest) {
            $transportRequests[] = $transportRequest;
        }

        $this->assertTrue($this->getUnitUnderTest()->hasOptimalPath($transportRequests));
    }

    private function getUnitUnderTest(): PythonVehicleRoutingWrapper
    {
        return new PythonVehicleRoutingWrapper();
    }
}

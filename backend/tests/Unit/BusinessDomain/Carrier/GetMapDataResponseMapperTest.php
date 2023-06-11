<?php

namespace Tests\Unit\BusinessDomain\Carrier;

use App\BusinessDomain\Carrier\GetMapDataResponseMapper;
use App\BusinessDomain\VehicleRouting\DTO\Edge;
use App\Facades\Map;
use Tests\TestCase;

class GetMapDataResponseMapperTest extends TestCase
{
    public function testResponseArrayStructure(): void
    {
        $mapper = $this->getUnitUnderTest();
        $actual = $mapper->mapResponse(Map::get(), [new Edge(1 ,100, 1, 2)]);
        $node = $actual['nodes'][0];
        $edge = $actual['edges'][0];

        $this->assertArrayHasKey('nodes', $actual);
        $this->assertArrayHasKey('edges', $actual);

        $this->assertArrayHasKey('id', $node);
        $this->assertArrayHasKey('x', $node);
        $this->assertArrayHasKey('y', $node);
        $this->assertArrayHasKey('size', $node);
        $this->assertEquals(1, $node['size']);

        $this->assertEquals(1, \count($actual['edges']));
        $this->assertArrayHasKey('id', $edge);
        $this->assertArrayHasKey('source', $edge);
        $this->assertArrayHasKey('target', $edge);
        $this->assertEquals(1, $edge['source']);
        $this->assertEquals(2, $edge['target']);
        $this->assertEquals('#FF0000', $edge['color']);
    }

    private function getUnitUnderTest(): GetMapDataResponseMapper
    {
        return new GetMapDataResponseMapper();
    }
}

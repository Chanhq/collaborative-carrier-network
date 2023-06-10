<?php

namespace Tests\Unit\Infrastructure\Map\DistanceCalculation;

use App\Infrastructure\Map\DistanceCalculation\EuclideanDistanceCalculator;
use Fhaculty\Graph\Vertex;
use Mockery\MockInterface;
use Tests\TestCase;

class EuclideanDistanceCalculatorTest extends TestCase
{
    public function testCalculatesTheDistanceCorrectly()
    {
        $calculator = $this->getUnitUnderTest();

        $vertex1 = $this->mock(Vertex::class, function (MockInterface $mock) {
            $mock->expects('getAttribute')->with('x')->andReturns('10');
            $mock->expects('getAttribute')->with('y')->andReturns('10');
        });


        $vertex2 = $this->mock(Vertex::class, function (MockInterface $mock) {
            $mock->expects('getAttribute')->with('x')->andReturns('21');
            $mock->expects('getAttribute')->with('y')->andReturns('24');
        });


        $this->assertEquals(18, $calculator->calculateDistance($vertex1, $vertex2));

        $vertex3 = $this->mock(Vertex::class, function (MockInterface $mock) {
            $mock->expects('getAttribute')->with('x')->andReturns('10');
            $mock->expects('getAttribute')->with('y')->andReturns('10');
        });


        $vertex4 = $this->mock(Vertex::class, function (MockInterface $mock) {
            $mock->expects('getAttribute')->with('x')->andReturns('21');
            $mock->expects('getAttribute')->with('y')->andReturns('23');
        });


        $this->assertEquals(17, $calculator->calculateDistance($vertex3, $vertex4));

        $vertex5 = $this->mock(Vertex::class, function (MockInterface $mock) {
            $mock->expects('getAttribute')->with('x')->andReturns('0');
            $mock->expects('getAttribute')->with('y')->andReturns('0');
        });


        $vertex6 = $this->mock(Vertex::class, function (MockInterface $mock) {
            $mock->expects('getAttribute')->with('x')->andReturns('3');
            $mock->expects('getAttribute')->with('y')->andReturns('4');
        });


        $this->assertEquals(5, $calculator->calculateDistance($vertex5, $vertex6));
    }
    private function getUnitUnderTest(): EuclideanDistanceCalculator
    {
        return new EuclideanDistanceCalculator();
    }
}

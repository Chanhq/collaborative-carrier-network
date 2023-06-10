<?php

namespace Tests\Unit\Infrastructure\GraphML;

use App\Infrastructure\GraphML\GraphMlExporter;
use Fhaculty\Graph\Graph;

class GraphMlExporterTest extends GraphMlBaseTestCase
{
    private $exporter;

    /**
     * @before
     */
    public function setUpExporter(): void
    {
        $this->exporter = new GraphMlExporter();
    }

    /**
     * @throws \Exception
     */
    public function testEmpty(): void
    {
        $graph = new Graph();

        $output = $this->exporter->getOutput($graph);
        $xml = new \SimpleXMLElement($output);

        $this->assertEquals(1, count($xml));
        $this->assertEquals(1, count($xml->graph));
        $this->assertEquals(0, count($xml->graph->children()));
    }

    /**
     * @throws \Exception
     */
    public function testSimple(): void
    {
        // 1 -- 2
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $v1->createEdge($v2);

        $output = $this->exporter->getOutput($graph);
        $xml = new \SimpleXMLElement($output);

        $this->assertEquals(1, count($xml->graph->edge));

        $edgeElem = $xml->graph->edge;
        $this->assertEquals('1', (string)$edgeElem['source']);
        $this->assertEquals('2', (string)$edgeElem['target']);
        $this->assertFalse(isset($edgeElem['directed']));
    }

    /**
     * @throws \Exception
     */
    public function testSimpleDirected(): void
    {
        // 1 -> 2
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $v1->createEdgeTo($v2);

        $output = $this->exporter->getOutput($graph);
        $xml = new \SimpleXMLElement($output);

        $this->assertEquals(1, count($xml->graph->edge));

        $edgeElem = $xml->graph->edge;
        $this->assertEquals('1', (string)$edgeElem['source']);
        $this->assertEquals('2', (string)$edgeElem['target']);
        $this->assertEquals('true', (string)$edgeElem['directed']);
    }
}

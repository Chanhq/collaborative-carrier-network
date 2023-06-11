<?php

namespace Tests\Unit\Infrastructure\GraphML;

use App\Infrastructure\GraphML\GraphMlLoader;

class GraphMlLoaderTest extends GraphMlBaseTestCase
{
    private GraphMlLoader $loader;

    public function setUp(): void
    {
        $this->loader = new GraphMlLoader();
    }

    public function testEmpty(): void
    {
        $data = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<graphml xmlns="http://graphml.graphdrawing.org/xmlns"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://graphml.graphdrawing.org/xmlns
     http://graphml.graphdrawing.org/xmlns/1.0/graphml.xsd">
  <graph id="G" edgedefault="undirected">
  </graph>
</graphml>
EOL;

        $graph = $this->loader->loadContents($data);

        $this->assertCount(0, $graph->getVertices());
    }

    /**
     * @throws \Exception
     */
    public function testSimpleGraph(): void
    {
        $data = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<graphml xmlns="http://graphml.graphdrawing.org/xmlns"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://graphml.graphdrawing.org/xmlns
     http://graphml.graphdrawing.org/xmlns/1.0/graphml.xsd">
  <graph id="G" edgedefault="undirected">
    <node id="n0"/>
    <node id="n1"/>
    <node id="n2"/>
    <node id="n3"/>
    <node id="n4"/>
    <node id="n5"/>
    <node id="n6"/>
    <node id="n7"/>
    <node id="n8"/>
    <node id="n9"/>
    <node id="n10"/>
    <edge source="n0" target="n2"/>
    <edge source="n1" target="n2"/>
    <edge source="n2" target="n3"/>
    <edge source="n3" target="n5"/>
    <edge source="n3" target="n4"/>
    <edge source="n4" target="n6"/>
    <edge source="n6" target="n5"/>
    <edge source="n5" target="n7"/>
    <edge source="n6" target="n8"/>
    <edge source="n8" target="n7"/>
    <edge source="n8" target="n9"/>
    <edge source="n8" target="n10"/>
  </graph>
</graphml>
EOL;

        $graph = $this->loader->loadContents($data);

        $this->assertCount(11, $graph->getVertices());
        $this->assertCount(12, $graph->getEdges());
    }

    /**
     * @throws \Exception
     */
    public function testEdgeUndirected(): void
    {
        $data = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<graphml xmlns="http://graphml.graphdrawing.org/xmlns"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://graphml.graphdrawing.org/xmlns
     http://graphml.graphdrawing.org/xmlns/1.0/graphml.xsd">
  <graph id="G" edgedefault="undirected">
    <node id="n0"/>
    <edge source="n0" target="n0" directed="false"/>
    <edge source="n0" target="n0"/>
  </graph>
</graphml>
EOL;

        $graph = $this->loader->loadContents($data);

        $this->assertCount(2, $graph->getEdges());
        $this->assertInstanceOf('Fhaculty\Graph\Edge\Undirected', $graph->getEdges()->getEdgeFirst());
        $this->assertInstanceOf('Fhaculty\Graph\Edge\Undirected', $graph->getEdges()->getEdgeLast());
    }

    /**
     * @throws \Exception
     */
    public function testEdgeDirected()
    {
        $data = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<graphml xmlns="http://graphml.graphdrawing.org/xmlns"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://graphml.graphdrawing.org/xmlns
     http://graphml.graphdrawing.org/xmlns/1.0/graphml.xsd">
  <graph id="G" edgedefault="directed">
    <node id="n0"/>
    <edge source="n0" target="n0" directed="true"/>
    <edge source="n0" target="n0"/>
  </graph>
</graphml>
EOL;

        $graph = $this->loader->loadContents($data);

        $this->assertCount(2, $graph->getEdges());
        $this->assertInstanceOf('Fhaculty\Graph\Edge\Directed', $graph->getEdges()->getEdgeFirst());
        $this->assertInstanceOf('Fhaculty\Graph\Edge\Directed', $graph->getEdges()->getEdgeLast());
    }

    /**
     * @throws \Exception
     */
    public function testAttributeDefault()
    {
        $data = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<graphml xmlns="http://graphml.graphdrawing.org/xmlns"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://graphml.graphdrawing.org/xmlns
     http://graphml.graphdrawing.org/xmlns/1.0/graphml.xsd">
  <key id="d0" for="node" attr.name="color" attr.type="string">
    <default>yellow</default>
  </key>
  <graph id="G" edgedefault="undirected">
        <node id="1" x="0" y="0"/>
        <node id="2" x="5" y="0"/>
        <edge source="1" target="2" weight="50" id="1"/>
  </graph>
</graphml>
EOL;

        $graph = $this->loader->loadContents($data);

        $vertex = $graph->getVertices()->getVertexFirst();

        $this->assertEquals('1', $vertex->getId());
        $this->assertEquals('0', $vertex->getAttribute('x'));
        $this->assertEquals('0', $vertex->getAttribute('y'));

        $edge = $graph->getEdges()->getEdgeFirst();

        $this->assertEquals('1', $edge->getAttribute('id'));
        $this->assertEquals('1', $edge->getAttribute('source'));
        $this->assertEquals('2', $edge->getAttribute('target'));
        $this->assertEquals('50', $edge->getWeight());
    }
}

<?php

namespace App\Infrastructure\GraphML;

use Fhaculty\Graph\Edge\Directed;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Graphp\Graph\Edge;
use http\Exception\RuntimeException;
use SimpleXMLElement;

class GraphMlExporter
{
    private const SKEL = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<graphml xmlns="http://graphml.graphdrawing.org/xmlns"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://graphml.graphdrawing.org/xmlns
     http://graphml.graphdrawing.org/xmlns/1.0/graphml.xsd">
</graphml>
EOL;

    public function getOutput(Graph $graph): string
    {
        $root = new SimpleXMLElement(self::SKEL);

        $graphElem = $root->addChild('graph');
        $graphElem['edgeDefault'] = 'undirected';

        foreach ($graph->getVertices()->getMap() as $id => $vertex) {
            /* @var $vertex Vertex */
            $vertexElem = $graphElem->addChild('node');
            $vertexElem['id'] = $id;
        }

        foreach ($graph->getEdges() as $edge) {
            /* @var $edge Edge */
            $edgeElem = $graphElem->addChild('edge');
            $edgeElem['source'] = $edge->getVertices()->getVertexFirst()->getId();
            $edgeElem['target'] = $edge->getVertices()->getVertexLast()->getId();
            $edgeElem->addAttribute('weight', $edge->getAttribute('weight'));
            $edgeElem->addAttribute('id', $edge->getAttribute('id'));
            if ($edge instanceof Directed) {
                $edgeElem['directed'] = 'true';
            }
        }
        $xmlData = $root->asXML();

        if ($xmlData === false) {
            throw new \RuntimeException('Could not convert map XML to string');
        }

        return $xmlData;
    }
}

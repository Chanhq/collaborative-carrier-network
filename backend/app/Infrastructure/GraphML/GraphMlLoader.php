<?php

namespace App\Infrastructure\GraphML;

use Fhaculty\Graph\Attribute\AttributeAware;
use Fhaculty\Graph\Graph;
use SimpleXMLElement;

class GraphMlLoader
{
    /**
     * @throws \Exception
     */
    public function loadContents(string $contents): Graph
    {
        return $this->loadXml(new SimpleXMLElement($contents));
    }

    private function loadXml(SimpleXMLElement $root): Graph
    {
        $graph = new Graph();

        // parse all attribute keys
        $keys = array();
        foreach ($root->key as $keyElem) {
            $keys[(string)$keyElem['id']] = array(
                'name' => (string)$keyElem['attr.name'],
                'type' => (string)$keyElem['attr.type'],
                'for'  => (isset($keyElem['for']) ? (string)$keyElem['for'] : 'all'),
                'default' => isset($keyElem->default)
                    ? $this->castAttribute((string)$keyElem->default, (string)$keyElem['attr.type'])
                    : null
            );
        }

        // load global graph settings
        $edgedefault = ((string)$root->graph['edgedefault'] === 'directed');
        $this->loadAttributes($root->graph, $graph, $keys);

        // load all vertices (known as "nodes" in GraphML)
        foreach ($root->graph->node as $nodeElem) {
            $vertex = $graph->createVertex((string)$nodeElem['id']);
            $vertex->setAttribute('x', (int)$nodeElem['x']);
            $vertex->setAttribute('y', (int)$nodeElem['y']);
            $this->loadAttributes($nodeElem, $vertex, $keys);
        }

        // load all edges
        foreach ($root->graph->edge as $edgeElem) {
            $source = $graph->getVertex((string)$edgeElem['source']);
            $target = $graph->getVertex((string)$edgeElem['target']);

            $directed = $edgedefault;
            if (isset($edgeElem['directed'])) {
                $directed = ((string)$edgeElem['directed'] === 'true');
            }

            if ($directed) {
                $edge = $source->createEdgeTo($target);
            } else {
                $edge = $source->createEdge($target);
            }
            $edge->setAttribute('id', $edgeElem['id']);
            $edge->setAttribute('source', $edgeElem['source']);
            $edge->setAttribute('target', $edgeElem['target']);
            $edge->setWeight((int)$edgeElem['weight']);
        }

        return $graph;
    }

    private function loadAttributes(SimpleXMLElement $xml, AttributeAware $target, array $keys): void
    {
        // apply all default values for this type
        $type = $xml->getName();
        foreach ($keys as $key) {
            if (isset($key['default']) && ($key['for'] === $type || $key['for'] === 'all')) {
                $target->setAttribute($key['name'], $key['default']);
            }
        }
        // apply all data attributes for this element
        foreach ($xml->data as $dataElem) {
            $key = $keys[(string)$dataElem['key']];
            $target->setAttribute($key['name'], $this->castAttribute((string)$dataElem, $key['type']));
        }
    }

    private function castAttribute($value, $type): bool|int|float|null
    {
        if ($type === 'boolean') {
            return ($value === 'true');
        } elseif ($type === 'int' || $type === 'long') {
            return (int)$value;
        } elseif ($type === 'float' || $type === 'double') {
            return (float)$value;
        }

        return null;
    }
}

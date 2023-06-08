<?php

namespace App\BusinessDomain\Carrier;

use App\BusinessDomain\VehicleRouting\DTO\Edge;
use Fhaculty\Graph\Edge\Base;
use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Set\Vertices;

class GetMapDataResponseMapper
{
    /**
     * @param Edge[] $optimalPath
     */
    public function mapEdgesToArray(Edges $edges, array $optimalPath): array
    {
        $mappedEdges = [];
        $edgeId = 1;
        foreach ($edges->getVector() as $edge) {
            if($this->isEdgeOnOptimalPath($edge, $optimalPath)) {
                $mappedEdges[] = [
                    'id' => $edgeId,
                    'source' => $edge->getVertices()->getVertexFirst()->getId(),
                    'target' => $edge->getVertices()->getVertexLast()->getId(),
                    'color' => '#FF0000',
                ];
            }
            $edgeId++;
        }

        return $mappedEdges;
    }

    public function mapVerticesToArray(Vertices $vertices): array
    {
        $mappedVertices = [];
        foreach ($vertices->getVector() as $vertex) {
            $mappedVertices[] = [
                'id' => (int)$vertex->getId(),
                'x' => (int)$vertex->getAttribute('x'),
                'y' => (int)$vertex->getAttribute('y'),
                'size' => 1,
            ];
        }
        return $mappedVertices;
    }

    /**
     * @param Edge[] $optimalPath
     */
    private function isEdgeOnOptimalPath(Base $edge, array $optimalPath): bool
    {
        foreach ($optimalPath as $optimalPathEdge) {
            $source = (int)$edge->getVertices()->getVertexFirst()->getId();
            $target = (int)$edge->getVertices()->getVertexLast()->getId();
            if (
                ($optimalPathEdge->target === $target && $optimalPathEdge->source === $source)
                || ($optimalPathEdge->target === $source && $optimalPathEdge->source === $target)
            ) {
                return true;
            }
        }
        return false;
    }
}

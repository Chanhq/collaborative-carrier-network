<?php

namespace App\BusinessDomain\Carrier;

use App\BusinessDomain\VehicleRouting\DTO\Edge;
use Fhaculty\Graph\Edge\Base;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Set\Vertices;

class GetMapDataResponseMapper
{
    /**
     * @param Graph $map
     * @param Edge[] $optimalPath
     * @return  array{edges: array<array{id: int, source: int, target: int, color: string}>,nodes: array<array{id: int, x: int, y: int, size: int}>}
     */
    public function mapResponse(Graph $map, array $optimalPath): array
    {
        return [
            'edges' => $this->mapEdgesToArray($map->getEdges(), $optimalPath),
            'nodes' => $this->mapVerticesToArray($map->getVertices()),
        ];
    }

    /**
     * @param Edge[] $optimalPath
     * @return array<array{
     *      id: int,
     *      source: int,
     *      target: int,
     *      color: string,
     * }>
     */
    private function mapEdgesToArray(Edges $edges, array $optimalPath): array
    {
        $mappedEdges = [];
        $edgeId = 1;
        foreach ($edges->getVector() as $edge) {
            if ($this->isEdgeOnOptimalPath($edge, $optimalPath)) {
                $mappedEdges[] = [
                    'id' => $edgeId,
                    'source' => (int)$edge->getVertices()->getVertexFirst()->getId(),
                    'target' => (int)$edge->getVertices()->getVertexLast()->getId(),
                    'color' => '#FF0000',
                ];
            }
            $edgeId++;
        }

        return $mappedEdges;
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

    /**
     * @return array<array{
     *      id: int,
     *      x: int,
     *      y: int,
     *      size: int   ,
     * }>
     */
    private function mapVerticesToArray(Vertices $vertices): array
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
}

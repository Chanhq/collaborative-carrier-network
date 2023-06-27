<?php

namespace App\BusinessDomain\Carrier;

use App\BusinessDomain\VehicleRouting\DTO\Edge;
use Fhaculty\Graph\Edge\Base;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Set\Vertices;

class GetMapDataResponseMapper
{
    private const COLOR_PATH_EDGE  = '#FF0000';

    private const COLOR_DEPOT_NODE = '#00FF00';

    private const COLOR_NODE  = '#000000';

    /**
     * @param Graph $map
     * @param Edge[] $optimalPath
     * @return  array{
     *      edges: array<array{id: int, source: int, target: int, color: string}>,
     *      nodes: array<array{id: int, x: int, y: int, size: float}>
     *}
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
        foreach ($edges->getVector() as $edge) {
            if ($this->isEdgeOnOptimalPath($edge, $optimalPath)) {
                $mappedEdges[] = [
                    'id' => (int)$edge->getAttribute('id'),
                    'source' => (int)$edge->getVertices()->getVertexFirst()->getId(),
                    'target' => (int)$edge->getVertices()->getVertexLast()->getId(),
                    'color' => self::COLOR_PATH_EDGE,
                ];
            }
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
     *      size: float,
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
                'size' => (int)$vertex->getId() === 1 ? 2.0 : 1.5,
                'color' => (int)$vertex->getId() === 1 ? self::COLOR_DEPOT_NODE : self::COLOR_NODE,
                'label' => (int)$vertex->getId() === 1 ? '' : 'ID: ' . $vertex->getId(),
            ];
        }
        return $mappedVertices;
    }
}

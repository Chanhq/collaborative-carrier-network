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
     * @return array<array{
 *          weight: int,
     *      source: int,
     *      target: int,
     *      isOnOptimalPath: bool,
 *     }>
     */
    public function mapEdgesToArray(Edges $edges, array $optimalPath): array
    {
        $mappedEdges = [];
        foreach ($edges->getVector() as $edge) {
            $mappedEdges[] = [
                'weight' => (int)$edge->getWeight(),
                'source' => (int)$edge->getVertices()->getVertexFirst()->getId(),
                'target' => (int)$edge->getVertices()->getVertexLast()->getId(),
                'isOnOptimalPath' => $this->isEdgeOnOptimalPath($edge, $optimalPath),
            ];
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
     * @return array <
     *      array{
     *          id: int,
     *          x: int,
     *          y: int,
     *      }
     *  >
     */
    public function mapVerticesToArray(Vertices $vertices): array
    {
        $mappedVertices = [];
        foreach ($vertices->getVector() as $vertex) {
            $mappedVertices[] = [
                'id' => (int)$vertex->getId(),
                'x' => (int)$vertex->getAttribute('x'),
                'y' => (int)$vertex->getAttribute('y'),
            ];
        }
        return $mappedVertices;
    }
}

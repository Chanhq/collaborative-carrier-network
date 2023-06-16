<?php

namespace App\BusinessDomain\VehicleRouting;

use App\BusinessDomain\VehicleRouting\DTO\Edge;
use App\Facades\Map;
use App\Models\TransportRequest;
use Fhaculty\Graph\Edge\Base;
use Illuminate\Support\Facades\Http;

class PythonVehicleRoutingWrapper
{
    /**
     * @param TransportRequest[] $transportRequests
     * @throws \JsonException
     */
    public function hasOptimalPath(array $transportRequests): bool
    {
        return $this->findOptimalPath($transportRequests) !== [];
    }

    /**
     * @param TransportRequest[] $transportRequests
     * @return Edge[]
     * @throws \JsonException
     */
    public function findOptimalPath(array $transportRequests): array
    {
        if (empty($transportRequests)) {
            return [];
        }
        $transportRequestsFiltered = [];

        foreach ($transportRequests as $transportRequest) {
            $transportRequestsFiltered[] = [
                'origin_node' => $transportRequest->originNode(),
                'destination_node' => $transportRequest->destinationNode(),
            ];
        }

        $requestBody = [
            'transport_requests' => $transportRequestsFiltered,
            'map_xml' => Map::xml(),
        ];
        $jsonBody = json_encode($requestBody);

        if ($jsonBody === false) {
            $jsonBody = '';
        }

        $optimalPathJson = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withBody($jsonBody)
            ->get('localhost:5000')
            ->body();

        if ($optimalPathJson === '') {
            return [];
        }

        $optimalPathData = json_decode($optimalPathJson, true, 512, JSON_THROW_ON_ERROR);
        $map = Map::get();

        return array_map(function ($optimalPathEdge) use ($map) {
            $matchedMapEdge = $map->getEdges()->getEdgeMatch(function ($mapEdge) use ($optimalPathEdge) {
                /** @var Base $mapEdge */
                $source = (int)$mapEdge->getVertices()->getVertexFirst()->getId();
                $target = (int)$mapEdge->getVertices()->getVertexLast()->getId();
                return ($source === $optimalPathEdge['source'] && $target === $optimalPathEdge['target'])
                    || ($source === $optimalPathEdge['target'] && $target === $optimalPathEdge['source']);
            });

            return new Edge(
                id: (int)$matchedMapEdge->getAttribute('id'),
                weight: $optimalPathEdge['weight'],
                source: $optimalPathEdge['source'],
                target: $optimalPathEdge['target'],
            );
        }, $optimalPathData['optimal_path']);
    }
}

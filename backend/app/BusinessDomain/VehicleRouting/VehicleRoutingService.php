<?php

namespace App\BusinessDomain\VehicleRouting;

use App\BusinessDomain\VehicleRouting\DTO\Edge;
use App\Models\TransportRequest;
use Illuminate\Support\Facades\Process;

class VehicleRoutingService
{
    /**
     * @param TransportRequest[] $transportRequests
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
        $transportRequestsFiltered = [];

        foreach ($transportRequests as $transportRequest) {
            $transportRequestsFiltered[] = [
                'id' => $transportRequest->id,
                'origin_node' => $transportRequest->originNode(),
                'destination_node' => $transportRequest->destinationNode(),
            ];
        }

        $transportRequestsJson = json_encode($transportRequestsFiltered);
        $optimalPathJson = Process::run('python3 network/main.py  --transportrequests \'' . $transportRequestsJson . '\'')
        ->output();

        if ($optimalPathJson === '') {
            return [];
        }

        $optimalPathData = json_decode($optimalPathJson, true, 512, JSON_THROW_ON_ERROR);

        return array_map(function ($edge) {
            return new Edge(
                weight: $edge['weight'],
                source: $edge['source'],
                target: $edge['target'],
            );
        }, $optimalPathData['optimal_path']);
    }
}

<?php

namespace App\BusinessDomain\VehicleRouting;

use App\BusinessDomain\VehicleRouting\DTO\Edge;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Process;

class VehicleRoutingService
{
    /**
     * @return Edge[]
     */
    public function findOptimalPath(HasMany $transportRequests): array
    {
        $transportRequestsJson =
            json_encode($transportRequests->get(['id', 'origin_node', 'destination_node'])->toArray());

        $optimalPathJson =
            Process::run('python3 network/main.py  --transportrequests \'' . $transportRequestsJson . '\'')
            ->output();

        return [
            new Edge(id: 1, weight: 88, source: 1, target: 2),
            new Edge(id: 32, weight: 42, source: 2, target: 5),
            new Edge(id: 113, weight: 57, source: 5, target: 8),
            new Edge(id: 185, weight: 88, source: 8, target: 11),
        ];
    }
}

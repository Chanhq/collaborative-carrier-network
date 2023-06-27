<?php

namespace App\Http\Controllers;

use App\BusinessDomain\Carrier\GetMapDataResponseMapper;
use App\BusinessDomain\VehicleRouting\PythonVehicleRoutingWrapper;
use App\Facades\Map;
use App\Http\Requests\CreateTransportRequestRequest;
use App\Http\Requests\SetCostModelRequest;
use App\Models\TransportRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CarrierController extends Controller
{
    public function __construct(
        private readonly PythonVehicleRoutingWrapper $vehicleRoutingService,
        private readonly GetMapDataResponseMapper $responseMapper
    ) {
    }

    public function getMapData(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $map = Map::get();
        $transportRequests = [];

        /** @var TransportRequest $transportRequest */
        foreach ($user->transportRequests()->get() as $transportRequest) {
            $transportRequests[] = $transportRequest;
        }
        try {
            $optimalPath = $this->vehicleRoutingService->findOptimalPath($transportRequests);
        } catch (\JsonException $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Some error occurred when encoding json.',
                'data' => [],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new JsonResponse([
            'status' => 'success',
            'message' => '',
            'data' => [
                'map' => $this->responseMapper->mapResponse($map, $optimalPath),
            ]
        ]);
    }

    public function setCostModel(SetCostModelRequest $request): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            $user->update($request->toArray());
            $user->save();
        } catch (\Throwable $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return new JsonResponse([
                'status' => 'error',
                'message' => 'An error occurred while updating the cost model of the user!',
                'data' => [],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([
            'status' => 'success',
            'message' => '',
            'data' => [
                'user' => $user->toArray(),
            ]
        ]);
    }

    public function getCostModel(): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();
        } catch (\Throwable $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return new JsonResponse([
                'status' => 'error',
                'message' => 'An error occurred while retrieving the cost model of the user!',
                'data' => [],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $costModel = [
            'transport_request_minimum_revenue' => $user->transportRequestMinimumRevenue(),
            'transport_request_cost_base' => $user->transportRequestCostBase(),
            'transport_request_cost_variable' => $user->transportRequestCostVariable(),
            'transport_request_price_base' => $user->transportRequestPriceBase(),
            'transport_request_price_variable' => $user->transportRequestPriceVariable(),
        ];

        return new JsonResponse([
            'status' => 'success',
            'message' => '',
            'data' => [
                'cost_model' => $costModel,
            ]
        ]);
    }

    public function getTransportRequests(): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            return new JsonResponse([
                'status' => 'success',
                'message' => '',
                'data' => [
                    'transport_requests' => $user->transportRequests()->get()->toArray(),
                ]
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'An unknown error occurred.',
                'data' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addTransportRequest(CreateTransportRequestRequest $request): JsonResponse
    {
        try {
            if (
                TransportRequest::where([
                'origin_node' => $request->validated('origin_node'),
                'destination_node' => $request->validated('destination_node')
                ])->get()->first() !== null
            ) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Can not add already existing transport request a second time.',
                    'data' => [],
                ], Response::HTTP_CONFLICT);
            }

            /** @var User $user */
            $user = Auth::user();
            $currentTransportRequestSet = $user->transportRequests();
            $newTransportRequestSetArray = $this->convertTransportRequests($currentTransportRequestSet);

            $transportRequest = new TransportRequest([
                'origin_node' => $request->validated('origin_node'),
                'destination_node' => $request->validated('destination_node'),
            ]);

            $newTransportRequestSetArray[] = $transportRequest;
            if (!$this->vehicleRoutingService->hasOptimalPath($newTransportRequestSetArray)) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Can not add already transport request that would make the routing infeasible.',
                    'data' => [],
                ], Response::HTTP_CONFLICT);
            }

            $currentTransportRequestSet->save($transportRequest);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Successfully added transport request for current user!',
                'data' => [
                    'origin_node' => $transportRequest->originNode(),
                    'destination_node' => $transportRequest->destinationNode(),
                ],
            ], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'An unknown error occurred.',
                'data' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @return TransportRequest[]
     */
    private function convertTransportRequests(HasMany $transportRequests): array
    {
        $convertedTransportRequests = [];
        /** @var TransportRequest $transportRequest */
        foreach ($transportRequests->get() as $transportRequest) {
            $convertedTransportRequests[] = $transportRequest;
        }

        return $convertedTransportRequests;
    }
}

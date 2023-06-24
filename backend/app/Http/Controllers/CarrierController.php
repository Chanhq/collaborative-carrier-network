<?php

namespace App\Http\Controllers;

use App\BusinessDomain\Carrier\GetMapDataResponseMapper;
use App\BusinessDomain\VehicleRouting\PythonVehicleRoutingWrapper;
use App\Facades\Map;
use App\Http\Requests\SetCostModelRequest;
use App\Models\TransportRequest;
use App\Models\User;
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
}

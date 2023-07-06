<?php

namespace App\Http\Controllers;

use App\BusinessDomain\Carrier\GetMapDataResponseMapper;
use App\BusinessDomain\RevenueCalculation\Service\TransportPriceCalculationService;
use App\BusinessDomain\VehicleRouting\PythonVehicleRoutingWrapper;
use App\Facades\Map;
use App\Http\Requests\CreateTransportRequestRequest;
use App\Http\Requests\SetCostModelRequest;
use App\Infrastructure\Eloquent\HasManyRelationShipToArrayConverter;
use App\Models\Auction;
use App\Models\AuctionEvaluation;
use App\Models\TransportRequest;
use App\Models\User;
use App\Models\Enum\AuctionStatusEnum;
use App\Models\Enum\TransportRequestStatusEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CarrierController extends Controller
{
    public function __construct(
        private readonly PythonVehicleRoutingWrapper $vehicleRoutingService,
        private readonly GetMapDataResponseMapper $responseMapper,
        private readonly TransportPriceCalculationService $priceCalculationService,
        private readonly HasManyRelationShipToArrayConverter $toArrayConverter,
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

            /** @var Collection $activeAuctionsCollection */
            $activeAuctionsCollection = Auction::active()->get();
            /** @var Collection $inActiveAuctionsCollection */
            $inActiveAuctionsCollection = Auction::inactive()->get();

            if ($inActiveAuctionsCollection->isNotEmpty() || $activeAuctionsCollection->isNotEmpty()) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Can not add transport requests when there is an ongoing auction.',
                    'data' => [],
                ], Response::HTTP_CONFLICT);
            }

            /** @var User $user */
            $user = Auth::user();
            $currentTransportRequestSet = $user->transportRequests();

            $transportRequest = $this->createNewTransportRequest(
                $currentTransportRequestSet,
                $request->validated('origin_node'),
                $request->validated('destination_node'),
                $user,
            );

            if ($transportRequest === null) {
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

    public function getAuctionEvaluationData(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $auctionEvaluations = $user->auctionEvaluations()->get(['auction_id', 'revenue_gain', 'price_to_pay']);

        if ($auctionEvaluations->isEmpty()) {
            return new JsonResponse([
                'status' => 'success',
                'message' => 'No evaluation data found for user.',
                'data' => [],
            ], Response::HTTP_NO_CONTENT);
        }

        $auctionEvaluationData = $auctionEvaluations->sortBy('auction_id')->toArray();

        return new JsonResponse([
            'status' => 'success',
            'message' => '',
            'data' => $auctionEvaluationData,
        ]);
    }

    private function createNewTransportRequest(
        HasMany $currentTransportRequestSet,
        int $originNode,
        int $destinationNode,
        User $user,
    ): ?TransportRequest {
        /** @var array<TransportRequest> $newTransportRequestSetArray */
        $newTransportRequestSetArray = $this->toArrayConverter->convert($currentTransportRequestSet);

        $transportRequest = new TransportRequest([
            'origin_node' => $originNode,
            'destination_node' => $destinationNode,
        ]);

        $newTransportRequestSetArray[] = $transportRequest;
        if (!$this->vehicleRoutingService->hasOptimalPath($newTransportRequestSetArray)) {
            return null;
        }

        $user->setTransportRequestSetRevenuePreAuction(
            $this->priceCalculationService->calculatePriceForTransportRequestSet(
                $newTransportRequestSetArray,
                $user
            )
        );
        $user->save();

        return $transportRequest;
    }
    public function completeTransportRequests(): JsonResponse
    {
        $user = Auth::user();
        // Check if there is an ongoing auction
        /** @var Collection $activeAuctionsCollection */
        $activeAuctionsCollection = Auction::active()->get();

        /** @var Collection $inActiveAuctionsCollection */
        $inActiveAuctionsCollection = Auction::inactive()->get();

        if ($inActiveAuctionsCollection->isNotEmpty() || $activeAuctionsCollection->isNotEmpty()) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Can not complete transport requests when there are uncompleted auctions',
                'data' => [],
            ], 409);
        }

        // Set all transport requests of the user calling the endpoint to completed
        $user->transportRequests()->update(['status' => TransportRequestStatusEnum::Completed]);
        return new JsonResponse([
                        'status' => 'success',
                        'message' => 'Transport requests completed successfully.',
                        'data' => []
                    ], 200);
    }
}

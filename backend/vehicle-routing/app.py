from flask import Flask, request, jsonify, make_response

import json
import sys
import typer

from mapper import TransportrequestMapper, GraphMapper
from helper.OrToolsHelper import extract_optimal_path_from_solution, print_solution

from graphml_parser import GraphMLParser


from ortools.constraint_solver import routing_enums_pb2
from ortools.constraint_solver import pywrapcp

sys.path.append("../")

app = Flask(__name__)
@app.route('/', methods=['GET'])
def optimalpath():
    mapNodes = request.json.get('nodes')

    or_tool_data = {
        'distance_matrix': GraphMapper.to_distance_matrix(mapNodes),
        'pickups_deliveries': TransportrequestMapper.to_pickups_deliveries(request.json.get('transport_requests')),
        'depot': 0,
        'num_vehicles': 1,
    }

    manager = pywrapcp.RoutingIndexManager(len(or_tool_data['distance_matrix']),
                                           or_tool_data['num_vehicles'], or_tool_data['depot'])

    routing = pywrapcp.RoutingModel(manager)

    def distance_callback(from_index, to_index):
        """Returns the manhattan distance between the two nodes."""
        # Convert from routing variable Index to distance matrix NodeIndex.
        from_node = manager.IndexToNode(from_index)
        to_node = manager.IndexToNode(to_index)
        return or_tool_data['distance_matrix'][from_node][to_node]

    transit_callback_index = routing.RegisterTransitCallback(distance_callback)
    routing.SetArcCostEvaluatorOfAllVehicles(transit_callback_index)

    # Add Distance constraint.
    dimension_name = 'Distance'
    routing.AddDimension(
        transit_callback_index,
        0,  # no slack
        3000,  # vehicle maximum travel distance
        True,  # start cumul to zero
        dimension_name)
    distance_dimension = routing.GetDimensionOrDie(dimension_name)
    distance_dimension.SetGlobalSpanCostCoefficient(100)

    involvedNodes = list(set([item for sub_list in or_tool_data['pickups_deliveries'] for item in sub_list]))
    involvedNodes.append(0)

    for node in range(70):
        if node not in involvedNodes:
            routing.AddDisjunction([manager.NodeToIndex(node)], 10)

    for transport_request in or_tool_data['pickups_deliveries']:
        pickup_index = manager.NodeToIndex(transport_request[0])
        delivery_index = manager.NodeToIndex(transport_request[1])
        routing.AddPickupAndDelivery(pickup_index, delivery_index)
        routing.solver().Add(
            routing.VehicleVar(pickup_index) == routing.VehicleVar(
                delivery_index))
        routing.solver().Add(
            distance_dimension.CumulVar(pickup_index) <=
            distance_dimension.CumulVar(delivery_index))


    # Setting first solution heuristic.
    search_parameters = pywrapcp.DefaultRoutingSearchParameters()
    search_parameters.first_solution_strategy = (
        routing_enums_pb2.FirstSolutionStrategy.PARALLEL_CHEAPEST_INSERTION)

    # Solve the problem.
    solution = routing.SolveWithParameters(search_parameters)

    print_solution(or_tool_data, manager, routing, solution)
    # Print solution on console.
    if solution:
        return make_response(json.dumps(extract_optimal_path_from_solution(or_tool_data, manager, routing, solution)), 200)

    return make_response('', 200)

import json
import sys
import typer

from mapper import TransportrequestMapper, GraphMapper
from helper.OrToolsHelper import extract_optimal_path_from_solution

from typing import Optional
from graphml_parser import GraphMLParser


from ortools.constraint_solver import routing_enums_pb2
from ortools.constraint_solver import pywrapcp

sys.path.append("../")

app = typer.Typer()

@app.command()
def optimalpath(transportrequests: Optional[str] = None):
    parser = GraphMLParser()
    graph = parser.parse('/var/www/html/maps/default.graphml')

    or_tool_data = {
        'distance_matrix': GraphMapper.to_distance_matrix(graph),
        'pickups_deliveries': TransportrequestMapper.to_pickups_deliveries(transportrequests),
        'depot': 1,
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

    for request in or_tool_data['pickups_deliveries']:
        pickup_index = manager.NodeToIndex(request[0])
        delivery_index = manager.NodeToIndex(request[1])
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

    # Print solution on console.
    if solution:
        print(json.dumps(extract_optimal_path_from_solution(or_tool_data, manager, routing, solution)))

if __name__ == "__main__":
    app()

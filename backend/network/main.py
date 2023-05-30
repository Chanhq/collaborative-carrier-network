import sys
import typer

from mapper import transportrequest_mapper, graph_mapper
from typing import Optional
from pygraphml import Graph
from graphml_parser import GraphMLParser

sys.path.append("../")

app = typer.Typer()


def print_graph(g: Graph):
    for node in g.nodes():
        print('========' + node.id + '========')
        for edge in node.edges():
            print(edge.parent().id, edge.child().id, edge['weight'], edge.id)
            print('----------------------------------------')


@app.command()
def optimalpath(transportrequests: Optional[str] = None):
    parser = GraphMLParser()
    graph = parser.parse('../maps/default.graphml')

    or_tool_data = {
        'distance_matrix': graph_mapper.to_distance_matrix(graph),
        'pickups_deliveries': transportrequest_mapper.to_pickups_deliveries(transportrequests),
        'depot': 1,
        'num_vehicles': 1
    }

    print(or_tool_data)

    print_graph(graph)

if __name__ == "__main__":
    app()

from pygraphml import Graph

from helper.GraphHelper import get_edge_by_node_ids


def to_distance_matrix(graph: Graph) -> [[int]]:
    edges = graph.edges()
    vertex_count = len(graph.nodes())
    distance_matrix = []
    for row_node_id in range(0, vertex_count):
        row_vector = []
        for col_node_id in range(0, vertex_count):
            if col_node_id == row_node_id:
                row_vector.append(0)
            else:
                row_vector.append(
                    int(get_edge_by_node_ids(parent_node=row_node_id+1, child_node=col_node_id+1, edges=edges)['weight'])
                )
        distance_matrix.append(row_vector)

    return distance_matrix

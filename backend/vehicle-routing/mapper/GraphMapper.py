from pygraphml import Graph

from helper.GraphHelper import get_edge_by_node_ids

import math

def to_distance_matrix(mapNodes) -> [[int]]:
    distance_matrix = []
    for row_node in mapNodes:
        row_vector = []
        for col_node in mapNodes:
            if row_node.get('id') == col_node.get('id'):
                row_vector.append(0)
            else:
                row_vector.append(
                    calculate_distance(int(row_node['x']), int(row_node['y']), int(col_node['x']), int(col_node['y']))
                )
        distance_matrix.append(row_vector)
    return distance_matrix

def calculate_distance(x1, y1, x2, y2):
    return round(math.sqrt((x1-x2)**2+(y1-y2)**2))

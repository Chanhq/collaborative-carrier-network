from pygraphml import Edge, Graph


def print_graph(g: Graph):
    for node in g.nodes():
        print('========' + node.id + '========')
        for edge in node.edges():
            print(edge.parent().id, edge.child().id, edge['weight'], edge.id)
            print('----------------------------------------')


def get_edge_by_node_ids(parent_node: int, child_node: int, edges: [Edge]) -> Edge:
    for edge in edges:
        if (int(edge.parent().id) == parent_node and int(edge.child().id) == child_node) or (int(edge.parent().id) == child_node and int(edge.child().id) == parent_node):
            return edge

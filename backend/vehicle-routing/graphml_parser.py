# -*- coding: utf-8 -*-

from __future__ import unicode_literals
from __future__ import division
from __future__ import absolute_import
from __future__ import print_function

from xml.dom import minidom

from pygraphml import Graph


class GraphMLParser:
    """
    """

    def __init__(self):
        """
        """

    def parse(self, f):
        """
        """

        g = None
        dom = minidom.parseString(f)
        root = dom.getElementsByTagName("graphml")[0]
        graph = root.getElementsByTagName("graph")[0]
        name = graph.getAttribute('id')

        g = Graph(name)

        # # Get attributes
        # attributes = []
        # for attr in root.getElementsByTagName("key"):
        #     attributes.append(attr)

        # Get nodes
        for node in graph.getElementsByTagName("node"):
            n = g.add_node(id=node.getAttribute('id'))

            for attr in node.getElementsByTagName("data"):
                if attr.firstChild:
                    n[attr.getAttribute("key")] = attr.firstChild.data
                else:
                    n[attr.getAttribute("key")] = ""

        # Get edges
        for edge in graph.getElementsByTagName("edge"):
            source = edge.getAttribute('source')
            dest = edge.getAttribute('target')

            # source/target attributes refer to IDs: http://graphml.graphdrawing.org/xmlns/1.1/graphml-structure.xsd
            e = g.add_edge_by_id(source, dest)
            e.id = str(int(e.id) + 1)
            e['weight'] = edge.getAttribute('weight')

            for attr in edge.getElementsByTagName("data"):
                if attr.firstChild:
                    e[attr.getAttribute("key")] = attr.firstChild.data
                else:
                    e[attr.getAttribute("key")] = ""

        return g


if __name__ == '__main__':

    parser = GraphMLParser()
    g = parser.parse('test.graphml')

    g.show(True)

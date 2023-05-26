<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Graphp\Graph\Graph;
use Graphp\Graph\Vertex;

//With this implementation, the CreateDefaultMap command will create a default 2D map with an Adjacency Matrix, store the matrix in the adjacency_matrix.txt file, and generate a graph based on the relationships defined in the matrix.
class CreateDefaultMap extends Command
{
    protected $signature = 'map:create';
    protected $description = 'Create a default 2D map with an Adjacency Matrix and generate a graph';

    public function handle()
    {
        $mapSize = 51; // Size of the map

        // Create an empty Adjacency Matrix as a string
        $adjacencyMatrixString = '';

        // Create a Graph
        $graph = new Graph();

        // Define the relationships between points on the map
        $x1 = 10;
        $y1 = 20;
        $x2 = 30;
        $y2 = 40;

        // Set the relationships in the Adjacency Matrix and add edges to the graph
        for ($i = 0; $i < $mapSize; $i++) {
            for ($j = 0; $j < $mapSize; $j++) {
                if (($i == $x1 && $j == $y1) || ($i == $x2 && $j == $y2)) {
                    $adjacencyMatrixString .= '1 ';
                    $graph->createVertex(['name' => $i . ',' . $j]);
                } else {
                    $adjacencyMatrixString .= '0 ';
                }
            }
            $adjacencyMatrixString .= "\n";
        }

        // Output the Adjacency Matrix string to a file
        $filePath = storage_path('app/adjacency_matrix.txt');
        file_put_contents($filePath, $adjacencyMatrixString);

        $this->info('Default map with Adjacency Matrix has been created and stored in adjacency_matrix.txt');

        // Generate the graph based on the Adjacency Matrix
        $this->generateGraphFromMatrix($filePath, $graph);

        $this->info('Graph has been generated based on the Adjacency Matrix');
    }

    private function generateGraphFromMatrix(string $filePath, Graph $graph)
    {
        // Read the adjacency matrix file
        $matrixContent = file_get_contents($filePath);

        // Split the matrix content into rows
        $rows = explode("\n", trim($matrixContent));

        // Get the map size based on the number of rows
        $mapSize = count($rows);

        // Add vertices to the graph
        for ($i = 0; $i < $mapSize; $i++) {
            for ($j = 0; $j < $mapSize; $j++) {
                // Check if there is a relationship between the points
                if ($rows[$i][$j] == '1') {
                    $graph->createVertex(['name' => $i . ',' . $j]);
                }
            }
        }

        // Add edges to the graph based on the relationships
        for ($i = 0; $i < $mapSize; $i++) {
            for ($j = 0; $j < $mapSize; $j++) {
                // Check if there is a relationship between the points
                if ($rows[$i][$j] == '1') {
                    $sourceVertex = null;

                    // Find the source vertex based on its name
                    $vertices = $graph->getVertices();
                    foreach ($vertices as $vertex) {
                        if ($vertex->getAttribute('name') === $i . ',' . $j) {
                            $sourceVertex = $vertex;
                            break;
                        }
                    }

                    // Check if the source vertex was found
                    if (!$sourceVertex) {
                        continue; // Skip to the next iteration
                    }

                    // Check neighboring vertices
                    $neighbors = [
                        [$i - 1, $j], // Above
                        [$i + 1, $j], // Below
                        [$i, $j + 1], // Right
                        [$i, $j - 1], // Left
                    ];

                    foreach ($neighbors as $neighbor) {
                        $neighborX = $neighbor[0];
                        $neighborY = $neighbor[1];

                        // Check if the neighboring point is within the map boundaries
                        if ($neighborX >= 0 && $neighborX < $mapSize && $neighborY >= 0 && $neighborY < $mapSize) {
                            // Check if there is a relationship with the neighboring point
                            if ($rows[$neighborX][$neighborY] == '1') {
                                $targetVertex = null;

                                // Find the target vertex based on its name
                                foreach ($vertices as $vertex) {
                                    if ($vertex->getAttribute('name') === $neighborX . ',' . $neighborY) {
                                        $targetVertex = $vertex;
                                        break;
                                    }
                                }

                                // Check if the target vertex was found
                                if ($targetVertex) {
                                    // Create an edge between the source and target vertices
                                    $graph->createEdgeDirected($sourceVertex, $targetVertex);
                                }
                            }
                        }
                    }
                }
            }
        }
    }


}

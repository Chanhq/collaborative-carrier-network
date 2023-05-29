<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CarrierController extends Controller
{
    public function getPath(Request $request)
    {
        // Validate the API token
        $validatedData = $request->validate([
            'api_token' => 'required'
        ]);

        // Call the Python script to calculate the path and retrieve the result
        $result = shell_exec('python /path/to/your/python/script.py');

        // Assuming the result is a JSON string, you can decode it
        $result = json_decode($result, true);

        // Create the content for the text file
        $content = "Adjacency Matrix:\n" . $result['matrix'] . "\n\nOptimal Path:\n" . $result['path'];

        // Create the response with the text file
        $response = Response::make($content, 200);
        $response->header('Content-Type', 'text/plain');
        $response->header('Content-Disposition', 'attachment; filename="matrix_and_path.txt"');

        return $response;
    }
}

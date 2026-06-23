<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Musae API",
 *     version="1.0.0",
 *     description="Documentation de l'API Musae générée avec Swagger"
 * )
 */
class SwaggerController {
    public function index()
    {
        return response()->json([
            'message' => 'Swagger documentation endpoint',
            'documentation_url' => url('/api/documentation') // Adjust this URL to your actual Swagger documentation endpoint
        ]);
        // This method can be used to return the Swagger JSON documentation
        // You can use a package like "zircote/swagger-php" to generate the documentation
        // and return it as a JSON response.
    }
}

<?php

namespace App\SwaggerAnnotations;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    security: [
        ["bearerAuth" => []]
    ]
)]
#[OA\Info(
    title: "Musae API",
    version: "1.0.0",
    description: "Documentation de l'API Musae générée avec Swagger"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
class OpenApiInfo {}

<?php

namespace App\SwaggerAnnotations;

use OpenApi\Attributes as OA;

#[OA\Tag(name: "Dashboard", description: "Endpoints du tableau de bord")]
class DashboardRoutes
{
    #[OA\Get(
        path: "/test",
        summary: "Route de test Swagger",
        tags: ["Dashboard"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Réponse OK"
            )
        ]
    )]
    public function test() {}
}

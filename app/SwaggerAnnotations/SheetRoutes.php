<?php

namespace App\SwaggerAnnotations;

use OpenApi\Attributes as OA;

#[OA\Tag(name: "Sheet", description: "Gestion des partitions")]
class SheetRoutes
{
    #[OA\Get(
        path: "/library",
        summary: "Liste des partitions",
        tags: ["Sheet"],
        responses: [
            new OA\Response(response: 200, description: "Liste des partitions")
        ]
    )]
    public function listLibrary() {}

    #[OA\Get(
        path: "/sheet/{sheet}",
        summary: "Détail d'une partition",
        tags: ["Sheet"],
        parameters: [
            new OA\Parameter(name: "sheet", in: "path", required: true)
        ],
        responses: [
            new OA\Response(response: 200, description: "Détails de la partition")
        ]
    )]
    public function detailSheet() {}

    #[OA\Post(
        path: "/sheet/store",
        summary: "Créer une partition",
        tags: ["Sheet"],
        requestBody: new OA\RequestBody(required: true),
        responses: [
            new OA\Response(response: 201, description: "Partition créée")
        ]
    )]
    public function storeSheet() {}
}

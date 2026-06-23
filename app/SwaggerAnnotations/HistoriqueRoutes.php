<?php

namespace App\SwaggerAnnotations;

use OpenApi\Attributes as OA;

#[OA\Tag(name: "Historique", description: "Historique des entraînements")]
class HistoriqueRoutes
{
    #[OA\Get(
        path: "/historique",
        summary: "Afficher l'historique",
        tags: ["Historique"],
        responses: [
            new OA\Response(response: 200, description: "Historique récupéré")
        ]
    )]
    public function historique() {}
}

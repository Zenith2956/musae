<?php

namespace App\SwaggerAnnotations;

use OpenApi\Attributes as OA;

#[OA\Tag(name: "Calendar", description: "Gestion du calendrier et des entraînements")]
class CalendarRoutes
{
    #[OA\Get(
        path: "/calendar/events",
        summary: "Liste des événements",
        tags: ["Calendar"],
        responses: [
            new OA\Response(response: 200, description: "Liste des événements")
        ]
    )]
    public function listEvents() {}

    #[OA\Post(
        path: "/calendar/events",
        summary: "Créer un événement",
        tags: ["Calendar"],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données de l'événement"
        ),
        responses: [
            new OA\Response(response: 201, description: "Événement créé")
        ]
    )]
    public function createEvent() {}

    #[OA\Put(
        path: "/calendar/events/{id}",
        summary: "Modifier un événement",
        tags: ["Calendar"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true)
        ],
        responses: [
            new OA\Response(response: 200, description: "Événement mis à jour")
        ]
    )]
    public function updateEvent() {}

    #[OA\Delete(
        path: "/calendar/events/{id}",
        summary: "Supprimer un événement",
        tags: ["Calendar"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true)
        ],
        responses: [
            new OA\Response(response: 204, description: "Événement supprimé")
        ]
    )]
    public function deleteEvent() {}

    #[OA\Get(
        path: "/calendar/sheets",
        summary: "Liste des partitions disponibles",
        tags: ["Calendar"],
        responses: [
            new OA\Response(response: 200, description: "Liste des partitions")
        ]
    )]
    public function listSheets() {}
}

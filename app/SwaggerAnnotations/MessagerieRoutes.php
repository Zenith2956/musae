<?php

namespace App\SwaggerAnnotations;

use OpenApi\Attributes as OA;

#[OA\Tag(name: "Messagerie", description: "Système de messagerie interne")]
class MessagerieRoutes
{
    #[OA\Get(
        path: "/messagerie",
        summary: "Liste des conversations",
        tags: ["Messagerie"],
        responses: [
            new OA\Response(response: 200, description: "Conversations récupérées")
        ]
    )]
    public function listConversations() {}

    #[OA\Post(
        path: "/messagerie",
        summary: "Créer une conversation",
        tags: ["Messagerie"],
        requestBody: new OA\RequestBody(required: true),
        responses: [
            new OA\Response(response: 201, description: "Conversation créée")
        ]
    )]
    public function createConversation() {}

    #[OA\Get(
        path: "/messagerie/{conversation}",
        summary: "Afficher une conversation",
        tags: ["Messagerie"],
        parameters: [
            new OA\Parameter(name: "conversation", in: "path", required: true)
        ],
        responses: [
            new OA\Response(response: 200, description: "Conversation récupérée")
        ]
    )]
    public function showConversation() {}

    #[OA\Post(
        path: "/messagerie/{conversation}/messages",
        summary: "Envoyer un message",
        tags: ["Messagerie"],
        parameters: [
            new OA\Parameter(name: "conversation", in: "path", required: true)
        ],
        requestBody: new OA\RequestBody(required: true),
        responses: [
            new OA\Response(response: 201, description: "Message envoyé")
        ]
    )]
    public function sendMessage() {}

    #[OA\Get(
        path: "/messagerie/{conversation}/messages",
        summary: "Lister les messages",
        tags: ["Messagerie"],
        parameters: [
            new OA\Parameter(name: "conversation", in: "path", required: true)
        ],
        responses: [
            new OA\Response(response: 200, description: "Messages récupérés")
        ]
    )]
    public function listMessages() {}

    #[OA\Delete(
        path: "/messagerie/messages/{message}",
        summary: "Supprimer un message",
        tags: ["Messagerie"],
        parameters: [
            new OA\Parameter(name: "message", in: "path", required: true)
        ],
        responses: [
            new OA\Response(response: 204, description: "Message supprimé")
        ]
    )]
    public function deleteMessage() {}
}

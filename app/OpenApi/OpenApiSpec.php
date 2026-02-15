<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    openapi: '3.0.3',
    info: new OA\Info(
        version: '1.0.0',
        description: "Documentation de l'API HelloCSE - Gestion des offres et produits",
        title: 'HelloCSE API'
    ),
    servers: [new OA\Server(url: '/api', description: 'API locale')]
)]
class OpenApiSpec {}

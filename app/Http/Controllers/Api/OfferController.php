<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\ListPublishedOffersAction;
use App\Enums\Pagination;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class OfferController extends Controller
{
    #[OA\Get(path: '/offers', operationId: 'listOffers', description: 'Retourne la liste paginée des offres publiées avec leurs produits publiés.', summary: 'Liste des offres publiées', tags: ['Offres'], parameters: [
        new OA\QueryParameter(
            name: 'page',
            description: 'Numéro de page pour la pagination',
            required: false,
            schema: new OA\Schema(type: 'integer', default: 1, minimum: 1)
        ),
    ], responses: [
        new OA\Response(
            response: 200,
            description: 'Liste des offres avec pagination',
            content: new OA\JsonContent(ref: '#/components/schemas/OfferListResponse')
        ),
    ])]
    public function index(
        ListPublishedOffersAction $listPublishedOffersAction
    ): AnonymousResourceCollection {
        return $listPublishedOffersAction->execute(Pagination::DefaultPerPage->value);
    }
}

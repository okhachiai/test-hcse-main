<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Actions\ListPublishedOffersAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ListOffersRequest;
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
        new OA\QueryParameter(
            name: 'per_page',
            description: 'Nombre d\'éléments par page (max 100)',
            required: false,
            schema: new OA\Schema(type: 'integer', default: 10, maximum: 100, minimum: 1)
        ),
    ], responses: [
        new OA\Response(
            response: 200,
            description: 'Liste des offres avec pagination',
            content: new OA\JsonContent(ref: '#/components/schemas/OfferListResponse')
        ),
    ])]
    public function index(
        ListOffersRequest $request,
        ListPublishedOffersAction $listPublishedOffersAction
    ): AnonymousResourceCollection {
        /** @var int $page */
        $page = $request->validated('page');
        /** @var int $perPage */
        $perPage = $request->validated('per_page');

        return $listPublishedOffersAction->execute($page, $perPage);
    }
}

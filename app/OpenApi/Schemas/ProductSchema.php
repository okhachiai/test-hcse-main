<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Product',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Produit XYZ'),
        new OA\Property(property: 'sku', type: 'string', example: 'SKU-1234-ABCD'),
        new OA\Property(property: 'image', type: 'string', nullable: true),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 99.99),
    ]
)]
class ProductSchema {}

<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Enums\ProductState;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

/**
 * @phpstan-type TFactory ProductFactory
 */
class Product extends Model
{
    /** @use HasFactory<TFactory> */
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'name',
        'sku',
        'image',
        'price',
        'state',
    ];

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'state' => ProductState::class,
        ];
    }

    /**
     * @return BelongsTo<Offer, $this>
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('state', ProductState::Published);
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    #[Scope]
    protected function draft(Builder $query): Builder
    {
        return $query->where('state', ProductState::Draft);
    }
}

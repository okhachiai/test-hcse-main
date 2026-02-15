<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Enums\OfferState;
use Database\Factories\OfferFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

/**
 * @phpstan-type TFactory OfferFactory
 */
class Offer extends Model
{
    /** @use HasFactory<TFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'image',
        'description',
        'state',
    ];

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'state' => OfferState::class,
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Product, $this>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @param  Builder<Offer>  $query
     * @return Builder<Offer>
     */
    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('state', OfferState::Published);
    }

    /**
     * @param  Builder<Offer>  $query
     * @return Builder<Offer>
     */
    #[Scope]
    protected function draft(Builder $query): Builder
    {
        return $query->where('state', OfferState::Draft);
    }

    /**
     * Offers visible in the public API (published only).
     *
     * @param  Builder<Offer>  $query
     * @return Builder<Offer>
     */
    #[Scope]
    protected function visibleForApi(Builder $query): Builder
    {
        return $this->published($query);
    }

    /**
     * @param  Builder<Offer>  $query
     * @return Builder<Offer>
     */
    #[Scope]
    protected function ofState(Builder $query, OfferState|string $state): Builder
    {
        $value = $state instanceof OfferState ? $state->value : $state;

        return $query->where('state', $value);
    }
}

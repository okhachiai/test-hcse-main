<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OfferState;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

class Offer extends Model
{
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
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

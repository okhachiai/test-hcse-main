<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Offer;
use App\Models\User;

class OfferPolicy
{
    public function view(User $user, Offer $offer): bool
    {
        return $offer->user_id === $user->id;
    }

    public function update(User $user, Offer $offer): bool
    {
        return $offer->user_id === $user->id;
    }

    public function delete(User $user, Offer $offer): bool
    {
        return $offer->user_id === $user->id;
    }
}

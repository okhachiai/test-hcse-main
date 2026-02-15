<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Offer;
use App\Models\User;

class OfferPolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function manage(User $user, Offer $offer): bool
    {
        return $offer->user_id === $user->id;
    }
}

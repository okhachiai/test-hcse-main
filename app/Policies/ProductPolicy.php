<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function manage(User $user, Product $product): bool
    {
        return $user->can('manage', $product->offer);
    }
}

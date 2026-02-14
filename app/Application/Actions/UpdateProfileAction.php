<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;

readonly class UpdateProfileAction
{
    public function execute(ProfileUpdateRequest $request): User
    {
        $user = auth()->user() ?? throw new AuthenticationException;
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return $user;
    }
}

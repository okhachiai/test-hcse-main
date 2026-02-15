<?php

declare(strict_types=1);

namespace App\Actions;

use App\Http\Requests\DeleteProfileRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

readonly class DeleteUserAction
{
    public function execute(DeleteProfileRequest $request): void
    {
        $user = auth()->user() ?? throw new AuthenticationException;

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}

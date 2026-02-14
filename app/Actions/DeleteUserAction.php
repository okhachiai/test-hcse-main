<?php

declare(strict_types=1);

namespace App\Actions;

use App\Http\Requests\DeleteProfileRequest;
use Illuminate\Support\Facades\Auth;

readonly class DeleteUserAction
{
    public function execute(DeleteProfileRequest $request): void
    {
        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}

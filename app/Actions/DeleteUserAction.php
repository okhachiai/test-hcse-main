<?php

declare(strict_types=1);

namespace App\Actions;

use App\Http\Requests\DeleteProfileRequest;
use Illuminate\Support\Facades\Auth;

readonly class DeleteUserAction
{
    public function execute(DeleteProfileRequest $request): ?bool
    {
        $user = $request->user();

        Auth::logout();

        $deleted = $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $deleted;
    }
}

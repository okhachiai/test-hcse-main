<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\DeleteUserAction;
use App\Actions\UpdateProfileAction;
use App\Http\Requests\DeleteProfileRequest;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request, UpdateProfileAction $updateProfileAction): RedirectResponse
    {
        $updateProfileAction->execute($request);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(DeleteProfileRequest $request, DeleteUserAction $deleteUserAction): RedirectResponse
    {
        $deleteUserAction->execute($request);

        return Redirect::to('/');
    }
}

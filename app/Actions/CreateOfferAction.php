<?php

declare(strict_types=1);

namespace App\Actions;

use App\Http\Requests\StoreOfferRequest;
use App\Repositories\OfferRepository;
use App\Services\ImageStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

readonly class CreateOfferAction
{
    public function __construct(
        private OfferRepository $offerRepository,
        private ImageStorage $imageStorage,
        private Redirector $redirector
    ) {}

    public function execute(StoreOfferRequest $request): RedirectResponse
    {
        $data = [
            'user_id' => $request->user()->id,
            'name' => $request->validated('name'),
            'slug' => $request->validated('slug'),
            'image' => $this->imageStorage->store($request->file('image'), 'offers'),
            'description' => $request->validated('description'),
            'state' => $request->validated('state'),
        ];

        $this->offerRepository->create($data);

        return $this->redirector->route('dashboard');
    }
}

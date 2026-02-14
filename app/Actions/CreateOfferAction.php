<?php

declare(strict_types=1);

namespace App\Actions;

use App\Http\Requests\StoreOfferRequest;
use App\Repositories\OfferRepository;
use App\Services\ImageStorage;
use Illuminate\Auth\AuthenticationException;

readonly class CreateOfferAction
{
    public function __construct(
        private OfferRepository $offerRepository,
        private ImageStorage $imageStorage
    ) {}

    public function execute(StoreOfferRequest $request): void
    {
        $data = [
            'user_id' => (auth()->user() ?? throw new AuthenticationException)->id,
            'name' => $request->validated('name'),
            'slug' => $request->validated('slug'),
            'image' => $this->imageStorage->store($request->file('image'), 'offers'),
            'description' => $request->validated('description'),
            'state' => $request->validated('state'),
        ];

        $this->offerRepository->create($data);
    }
}

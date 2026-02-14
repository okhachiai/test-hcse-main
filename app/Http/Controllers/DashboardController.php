<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function show(Request $request)
    {
        $offers = $request->state ? Offer::ofState($request->state) : Offer::query();

        if ($request->name) {
            $offers = $offers->where('name', 'like', "%{$request->name}%");
        }

        if ($request->slug) {
            $offers = $offers->where('slug', 'like', "%{$request->slug}%");
        }

        return view('dashboard', ['offers' => $offers->get()]);
    }
}

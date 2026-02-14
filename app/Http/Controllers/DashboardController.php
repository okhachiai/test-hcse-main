<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Actions\ListDashboardOffersAction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function show(Request $request, ListDashboardOffersAction $listDashboardOffersAction): View
    {
        $dashboardData = $listDashboardOffersAction->execute($request);

        return view('dashboard', [
            'offers' => $dashboardData->offers,
            'filterParams' => $dashboardData->filterParams,
            'activeState' => $dashboardData->activeState,
            'offerStates' => $dashboardData->offerStates,
        ]);
    }
}

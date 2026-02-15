<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Actions\ListDashboardOffersAction;
use App\Http\Requests\ListDashboardRequest;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function show(ListDashboardRequest $request, ListDashboardOffersAction $listDashboardOffersAction): View
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

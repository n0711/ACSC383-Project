<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Helpers;
use App\Core\Request;
use App\Core\Response;
use App\Services\DashboardService;

final class DashboardController
{
    public function index(Request $req): Response
    {
        if (!Auth::check()) return Response::redirect(Helpers::route('login'));

        $svc = new DashboardService();
        $summary = $svc->summary(Auth::id() ?? 0);

        return Response::html(\App\Core\View::render('dashboard/index', [
            'title' => 'Impact Dashboard',
            'summary' => $summary,
        ]));
    }

    public function history(Request $req): Response
    {
        if (!Auth::check()) return Response::redirect(Helpers::route('login'));

        $svc = new DashboardService();
        $rows = $svc->history(Auth::id() ?? 0);

        return Response::html(\App\Core\View::render('dashboard/history', [
            'title' => 'Impact History',
            'rows' => $rows,
        ]));
    }
}

<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\LandingService;

final class LandingController
{
    public function index(Request $req): Response
    {
        $svc = new LandingService();
        $data = $svc->platformImpact();

        return Response::html(\App\Core\View::render('landing/index', [
            'title' => 'Platform Impact',
            'data' => $data,
        ]));
    }
}

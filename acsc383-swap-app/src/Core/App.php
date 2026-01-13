<?php
declare(strict_types=1);

namespace App\Core;

use App\Controllers\AuthController;
use App\Controllers\ItemController;
use App\Controllers\TransactionController;
use App\Controllers\DashboardController;
use App\Controllers\LandingController;

final class App
{
    private static ?App $instance = null;
    private Router $router;

    public static function getInstance(): App
    {
        if (!self::$instance) {
            self::$instance = new App();
            self::$instance->boot();
        }
        return self::$instance;
    }

    private function boot(): void
    {
        $this->router = new Router();

        //Landing
        $this->router->get('landing', [LandingController::class, 'index']);

        // Auth
        $this->router->get('register', [AuthController::class, 'register']);
        $this->router->post('register', [AuthController::class, 'register']);
        $this->router->get('verify', [AuthController::class, 'verify']);
        $this->router->get('login', [AuthController::class, 'login']);
        $this->router->post('login', [AuthController::class, 'login']);
        $this->router->get('logout', [AuthController::class, 'logout']);

        // Items
        $this->router->get('browse', [ItemController::class, 'browse']);
        $this->router->get('item_show', [ItemController::class, 'show']);

        $this->router->get('my_items', [ItemController::class, 'mine']);

        $this->router->get('item_create', [ItemController::class, 'create']);
        $this->router->post('item_create', [ItemController::class, 'create']);

        $this->router->get('item_edit', [ItemController::class, 'edit']);
        $this->router->post('item_edit', [ItemController::class, 'edit']);

        $this->router->get('item_delete', [ItemController::class, 'delete']);
    
        // Transactions
        $this->router->get('tx_mine', [TransactionController::class, 'mine']);
        $this->router->get('tx_show', [TransactionController::class, 'show']);
        $this->router->get('tx_propose', [TransactionController::class, 'propose']);

        $this->router->get('tx_action', [TransactionController::class, 'action']); // accept/decline/cancel
        $this->router->post('tx_complete', [TransactionController::class, 'complete']);

        // Dashboard
        $this->router->get('impact', [DashboardController::class, 'index']);
        $this->router->get('impact_history', [DashboardController::class, 'history']);

        }

    public function handle(Request $request): Response
    {
        return $this->router->dispatch($request);
    }
}

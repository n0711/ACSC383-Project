<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Helpers;
use App\Core\Request;
use App\Core\Response;
use App\Services\TransactionService;

final class TransactionController
{
    public function mine(Request $req): Response
    {
        if (!Auth::check()) return Response::redirect(Helpers::route('login'));

        $svc = new TransactionService();
        $rows = $svc->myTransactions(Auth::id() ?? 0);

        return Response::html(\App\Core\View::render('tx/mine', [
            'title' => 'My transactions',
            'rows' => $rows
        ]));
    }

    public function propose(Request $req): Response
    {
        if (!Auth::check()) return Response::redirect(Helpers::route('login'));

        $itemId = (int)$req->input('item_id', '0');
        if ($itemId <= 0) return Response::redirect(Helpers::route('browse'));

        $svc = new TransactionService();
        $res = $svc->propose($itemId, Auth::id() ?? 0);

        if ($res['ok'] ?? false) {
            Helpers::flash('success', 'Proposal sent. Item reserved.');
            return Response::redirect(Helpers::route('tx_show', ['id' => (int)$res['transaction_id']]));
        }

        Helpers::flash('danger', (string)($res['error'] ?? 'Unable to propose.'));
        return Response::redirect(Helpers::route('item_show', ['id' => $itemId]));
    }

    public function show(Request $req): Response
    {
        if (!Auth::check()) return Response::redirect(Helpers::route('login'));
        $txId = (int)$req->input('id', '0');

        $svc = new TransactionService();
        $tx = $svc->get($txId);
        if (!$tx) {
            Helpers::flash('warning', 'Transaction not found.');
            return Response::redirect(Helpers::route('tx_mine'));
        }

        $uid = Auth::id() ?? 0;
        $isParty = ((int)$tx['seller_id'] === $uid) || ((int)$tx['buyer_id'] === $uid);
        if (!$isParty) {
            Helpers::flash('danger', 'Not allowed.');
            return Response::redirect(Helpers::route('tx_mine'));
        }

        return Response::html(\App\Core\View::render('tx/show', [
            'title' => 'Transaction',
            'tx' => $tx,
            'uid' => $uid
        ]));
    }

    public function action(Request $req): Response
    {
        if (!Auth::check()) return Response::redirect(Helpers::route('login'));

        $txId = (int)$req->input('id', '0');
        $act = $req->input('act');

        $svc = new TransactionService();
        $uid = Auth::id() ?? 0;

        $map = [
            'accept' => fn() => $svc->accept($txId, $uid),
            'decline' => fn() => $svc->decline($txId, $uid),
            'cancel' => fn() => $svc->cancel($txId, $uid),
        ];

        if (!isset($map[$act])) {
            Helpers::flash('warning', 'Unknown action.');
            return Response::redirect(Helpers::route('tx_show', ['id' => $txId]));
        }

        $res = $map[$act]();
        Helpers::flash(($res['ok'] ?? false) ? 'success' : 'danger', ($res['ok'] ?? false) ? 'Updated.' : (string)($res['error'] ?? 'Failed.'));
        return Response::redirect(Helpers::route('tx_show', ['id' => $txId]));
    }

    public function complete(Request $req): Response
    {
        if (!Auth::check()) return Response::redirect(Helpers::route('login'));

        $txId = (int)$req->input('id', '0');
        $uid = Auth::id() ?? 0;

        $proofPath = null;


        $svc = new TransactionService();
        $res = $svc->complete($txId, $uid, $proofPath);

        Helpers::flash(($res['ok'] ?? false) ? 'success' : 'danger', ($res['ok'] ?? false) ? 'Completed.' : (string)($res['error'] ?? 'Failed.'));
        return Response::redirect(Helpers::route('tx_show', ['id' => $txId]));
    }
}

<?php
declare(strict_types=1);

namespace App\Services;

use App\Database\Database;
use PDO;

final class TransactionService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    public function myTransactions(int $userId): array
    {
        $stmt = $this->pdo->prepare("
          SELECT t.*, i.title, i.owner_id, i.status AS item_status
          FROM transactions t
          JOIN items i ON i.item_id = t.item_id
          WHERE t.seller_id = ? OR t.buyer_id = ?
          ORDER BY t.created_at DESC
          LIMIT 100
        ");
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll() ?: [];
    }

    public function get(int $txId): ?array
    {
        $stmt = $this->pdo->prepare("
          SELECT t.*, i.title, i.description, i.owner_id, i.status AS item_status, i.transaction_type AS item_type
          FROM transactions t
          JOIN items i ON i.item_id = t.item_id
          WHERE t.transaction_id = ?
          LIMIT 1
        ");
        $stmt->execute([$txId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Create proposal:
     * - Only AVAILABLE items
     * - Buyer cannot be seller
     * - One active transaction at a time per item (simple rule)
     */
    public function propose(int $itemId, int $buyerId): array
    {
        return Database::transaction(function(PDO $pdo) use ($itemId, $buyerId) {
            $stmt = $pdo->prepare("SELECT * FROM items WHERE item_id=? LIMIT 1");
            $stmt->execute([$itemId]);
            $item = $stmt->fetch();
            if (!$item) return ['ok'=>false,'error'=>'Item not found.'];

            if ((string)$item['status'] !== 'AVAILABLE') return ['ok'=>false,'error'=>'Item is not available.'];
            $sellerId = (int)$item['owner_id'];
            if ($sellerId === $buyerId) return ['ok'=>false,'error'=>'You cannot propose on your own item.'];

            // Ensure no existing PENDING/ACCEPTED on the same item
            $stmt2 = $pdo->prepare("
              SELECT 1 FROM transactions
              WHERE item_id=? AND status IN ('PENDING','ACCEPTED')
              LIMIT 1
            ");
            $stmt2->execute([$itemId]);
            if ($stmt2->fetchColumn()) return ['ok'=>false,'error'=>'This item already has an active transaction.'];

            $type = (string)$item['transaction_type'];

            $stmt3 = $pdo->prepare("
              INSERT INTO transactions (item_id, seller_id, buyer_id, transaction_type, status, created_at)
              VALUES (?, ?, ?, ?, 'PENDING', NOW())
            ");
            $stmt3->execute([$itemId, $sellerId, $buyerId, $type]);
            $txId = (int)$pdo->lastInsertId();

            // Reserve the item during transaction period
            $pdo->prepare("UPDATE items SET status='RESERVED' WHERE item_id=?")->execute([$itemId]);

            return ['ok'=>true,'transaction_id'=>$txId];
        });
    }

    public function accept(int $txId, int $actorId): array
    {
        return Database::transaction(function(PDO $pdo) use ($txId, $actorId) {
            $tx = $this->get($txId);
            if (!$tx) return ['ok'=>false,'error'=>'Transaction not found.'];

            if ((int)$tx['seller_id'] !== $actorId) return ['ok'=>false,'error'=>'Only the seller can accept.'];
            if ((string)$tx['status'] !== 'PENDING') return ['ok'=>false,'error'=>'Not in PENDING state.'];

            $pdo->prepare("UPDATE transactions SET status='ACCEPTED', updated_at=NOW() WHERE transaction_id=?")->execute([$txId]);
            return ['ok'=>true];
        });
    }

    public function decline(int $txId, int $actorId): array
    {
        return $this->cancelLike($txId, $actorId, 'DECLINED');
    }

    public function cancel(int $txId, int $actorId): array
    {
        return $this->cancelLike($txId, $actorId, 'CANCELLED');
    }

    private function cancelLike(int $txId, int $actorId, string $newStatus): array
    {
        return Database::transaction(function(PDO $pdo) use ($txId, $actorId, $newStatus) {
            $tx = $this->get($txId);
            if (!$tx) return ['ok'=>false,'error'=>'Transaction not found.'];

            $isParty = ((int)$tx['seller_id'] === $actorId) || ((int)$tx['buyer_id'] === $actorId);
            if (!$isParty) return ['ok'=>false,'error'=>'Not allowed.'];

            if (!in_array((string)$tx['status'], ['PENDING','ACCEPTED'], true)) {
                return ['ok'=>false,'error'=>'Cannot update this transaction now.'];
            }

            $pdo->prepare("UPDATE transactions SET status=?, updated_at=NOW() WHERE transaction_id=?")
                ->execute([$newStatus, $txId]);

            // Release item back to AVAILABLE
            $pdo->prepare("UPDATE items SET status='AVAILABLE' WHERE item_id=?")
                ->execute([(int)$tx['item_id']]);

            return ['ok'=>true];
        });
    }

    /**
     * Complete:
     * - For SALE/DONATION 
     * - For SWAP: can complete without proof (simple)
     * - Delist item -> COMPLETED
     */
    public function complete(int $txId, int $actorId, ?string $proofPath): array
    {
        return Database::transaction(function(PDO $pdo) use ($txId, $actorId, $proofPath) {
            $tx = $this->get($txId);
            if (!$tx) return ['ok'=>false,'error'=>'Transaction not found.'];

            $isParty = ((int)$tx['seller_id'] === $actorId) || ((int)$tx['buyer_id'] === $actorId);
            if (!$isParty) return ['ok'=>false,'error'=>'Not allowed.'];

            if ((string)$tx['status'] !== 'ACCEPTED') return ['ok'=>false,'error'=>'Transaction must be ACCEPTED first.'];

            $type = (string)$tx['transaction_type'];
            

            $pdo->prepare("UPDATE transactions SET status='COMPLETED', updated_at=NOW() WHERE transaction_id=?")
                ->execute([$proofPath, $txId]);

            $pdo->prepare("UPDATE items SET status='COMPLETED' WHERE item_id=?")
                ->execute([(int)$tx['item_id']]);

            return ['ok'=>true];
        });
    }
}

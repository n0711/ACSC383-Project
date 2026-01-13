<?php
declare(strict_types=1);

namespace App\Services;

use App\Database\Database;
use PDO;

final class DashboardService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    /**
     * Summary totals for a user.
     * We count completed exchanges that involved the user:
     * - as seller/owner OR buyer/receiver
     *
     * totals are computed from items.weight_kg × dummy coefficients (by category).
     *
     * @return array<string,mixed>
     */
    public function summary(int $userId): array
    {
        $rows = $this->completedRows($userId);

        $totalCo2 = 0.0;
        $totalWaste = 0.0;

        $byCategory = []; // category => ['co2'=>x,'waste'=>y,'kg'=>z,'count'=>n]

        foreach ($rows as $r) {
            $cat = (string)$r['category'];
            $kg = (float)($r['weight_kg'] ?? 0);

            [$co2, $waste] = ImpactService::calculate($cat, $kg);

            $totalCo2 += $co2;
            $totalWaste += $waste;

            if (!isset($byCategory[$cat])) {
                $byCategory[$cat] = ['co2' => 0.0, 'waste' => 0.0, 'kg' => 0.0, 'count' => 0];
            }

            $byCategory[$cat]['co2'] += $co2;
            $byCategory[$cat]['waste'] += $waste;
            $byCategory[$cat]['kg'] += $kg;
            $byCategory[$cat]['count'] += 1;
        }

        // Sort categories by CO2 desc
        uasort($byCategory, fn($a, $b) => ($b['co2'] <=> $a['co2']));

        return [
            'total_co2' => round($totalCo2, 2),
            'total_waste' => round($totalWaste, 2),
            'completed_count' => count($rows),
            'by_category' => $byCategory,
        ];
    }

    /*
     * History rows for UI.
     */
    public function history(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
              t.transaction_id,
              t.transaction_type,
              t.status,
              t.created_at,
              t.updated_at,
              i.item_id,
              i.title,
              i.category,
              i.weight_kg,
              i.owner_id,
              t.seller_id,
              t.buyer_id
            FROM transactions t
            JOIN items i ON i.item_id = t.item_id
            WHERE t.status = 'COMPLETED'
              AND (t.seller_id = ? OR t.buyer_id = ?)
            ORDER BY COALESCE(t.updated_at, t.created_at) DESC
            LIMIT 200
        ");
        $stmt->execute([$userId, $userId]);
        $rows = $stmt->fetchAll() ?: [];

        // add computed impact to each row
        foreach ($rows as &$r) {
            $kg = (float)($r['weight_kg'] ?? 0);
            [$co2, $waste] = ImpactService::calculate((string)$r['category'], $kg);
            $r['impact_co2'] = $co2;
            $r['impact_waste'] = $waste;
        }
        unset($r);

        return $rows;
    }

    /*
     * Internal helper: all completed transaction rows for the user.
     */
    private function completedRows(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT i.category, i.weight_kg
            FROM transactions t
            JOIN items i ON i.item_id = t.item_id
            WHERE t.status = 'COMPLETED'
              AND (t.seller_id = ? OR t.buyer_id = ?)
        ");
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll() ?: [];
    }
}

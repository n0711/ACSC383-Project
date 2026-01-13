<?php
declare(strict_types=1);

namespace App\Services;

use App\Database\Database;
use PDO;

final class LandingService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    /*
     * Platform-wide impact:
     * We compute savings only from COMPLETED transactions.
     */
    public function platformImpact(): array
    {
        // Get total weight and count by category from COMPLETED transactions
        $stmt = $this->pdo->query("
            SELECT
              i.category,
              COUNT(*) AS completed_count,
              SUM(i.weight_kg) AS total_kg
            FROM transactions t
            JOIN items i ON i.item_id = t.item_id
            WHERE t.status = 'COMPLETED'
            GROUP BY i.category
            ORDER BY total_kg DESC
        ");
        $rows = $stmt->fetchAll() ?: [];

        $labels = [];
        $co2ByCat = [];
        $wasteByCat = [];
        $kgByCat = [];
        $countByCat = [];

        $totalCo2 = 0.0;
        $totalWaste = 0.0;
        $totalKg = 0.0;
        $totalCompleted = 0;

        foreach ($rows as $r) {
            $cat = (string)($r['category'] ?? 'Other');
            $kg = (float)($r['total_kg'] ?? 0);
            $cnt = (int)($r['completed_count'] ?? 0);

            [$co2, $waste] = ImpactService::calculate($cat, $kg);

            $labels[] = $cat;
            $kgByCat[] = round($kg, 2);
            $countByCat[] = $cnt;
            $co2ByCat[] = round($co2, 2);
            $wasteByCat[] = round($waste, 2);

            $totalKg += $kg;
            $totalCo2 += $co2;
            $totalWaste += $waste;
            $totalCompleted += $cnt;
        }

        return [
            'kpis' => [
                'total_co2' => round($totalCo2, 2),
                'total_waste' => round($totalWaste, 2),
                'total_kg' => round($totalKg, 2),
                'completed_count' => $totalCompleted,
            ],
            'series' => [
                'labels' => $labels,
                'kg' => $kgByCat,
                'count' => $countByCat,
                'co2' => $co2ByCat,
                'waste' => $wasteByCat,
            ],
        ];
    }
}

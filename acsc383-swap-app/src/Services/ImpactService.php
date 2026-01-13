<?php
declare(strict_types=1);

namespace App\Services;

final class ImpactService
{
    /**
     * Dummy coefficients (per kg of item weight).
     * Later: replace with real factors / dataset.
     *
     * return: [co2_per_kg, waste_per_kg]
     */
    public static function coeff(string $category): array
    {
        $map = [
            'Electronics' => [2.40, 0.80],
            'Furniture'   => [1.10, 1.60],
            'Clothing'    => [6.50, 0.30],
            'Books'       => [1.20, 0.40],
            'Kitchen'     => [1.80, 0.90],
            'Sports'      => [2.20, 0.70],
            'Other'       => [2.00, 0.60],
        ];
        return $map[$category] ?? $map['Other'];
    }

    /**
     * Calculate totals using kg and dummy coefficients.
     * return: [co2_total_kg, waste_total_kg]
     */
    public static function calculate(string $category, float $weightKg): array
    {
        $weightKg = max(0.0, $weightKg);
        [$co2PerKg, $wastePerKg] = self::coeff($category);

        $co2 = $weightKg * (float)$co2PerKg;
        $waste = $weightKg * (float)$wastePerKg;

        // formating with 2 decimals
        return [round($co2, 2), round($waste, 2)];
    }
}

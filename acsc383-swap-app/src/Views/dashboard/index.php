<?php
declare(strict_types=1);

use App\Core\Helpers;

$summary = $summary ?? [];
$totalCo2 = (string)($summary['total_co2'] ?? '0');
$totalWaste = (string)($summary['total_waste'] ?? '0');
$completedCount = (int)($summary['completed_count'] ?? 0);
$byCategory = $summary['by_category'] ?? [];
?>
<div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
  <div>
    <div class="h4 mb-1">Impact Dashboard</div>
    <div class="app-muted">Dummy coefficients × item weight (kg). Refined later.</div>
  </div>
  <a class="btn app-btn-soft" href="<?= Helpers::route('impact_history') ?>">View history</a>
</div>

<div class="row g-3">
  <div class="col-12 col-lg-4">
    <div class="app-card p-4 h-100">
      <div class="fw-semibold mb-1">Total CO₂ saved</div>
      <div class="display-6 fw-semibold"><?= Helpers::e($totalCo2) ?> <span class="fs-6 app-muted">kg</span></div>
      <div class="small app-muted mt-2">Estimated from completed exchanges you were involved in.</div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="app-card p-4 h-100">
      <div class="fw-semibold mb-1">Total waste diverted</div>
      <div class="display-6 fw-semibold"><?= Helpers::e($totalWaste) ?> <span class="fs-6 app-muted">kg</span></div>
      <div class="small app-muted mt-2">Category average coefficients (placeholder).</div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="app-card p-4 h-100">
      <div class="fw-semibold mb-1">Completed exchanges</div>
      <div class="display-6 fw-semibold"><?= (int)$completedCount ?></div>
      <div class="small app-muted mt-2">More completed exchanges → larger impact totals.</div>
    </div>
  </div>
</div>

<div class="mt-4 app-divider"></div>

<div class="mt-4">
  <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-2">
    <div>
      <div class="h5 mb-1">By category</div>
      <div class="app-muted">Sorted by CO₂ contribution.</div>
    </div>
  </div>

  <?php if (empty($byCategory)): ?>
    <div class="app-card p-4">
      <div class="h6 mb-1">No completed exchanges yet</div>
      <div class="app-muted">Complete a swap/sale/donation to see totals here.</div>
    </div>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($byCategory as $cat => $m): ?>
        <div class="col-12 col-md-6 col-lg-4">
          <div class="app-card p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
              <div class="fw-semibold"><?= Helpers::e((string)$cat) ?></div>
              <span class="badge badge-soft"><?= (int)$m['count'] ?> items</span>
            </div>

            <div class="mt-3 small">
              <div class="d-flex justify-content-between">
                <span class="app-muted">Total kg</span>
                <span class="text-white fw-semibold"><?= Helpers::e((string)round((float)$m['kg'], 2)) ?></span>
              </div>
              <div class="d-flex justify-content-between">
                <span class="app-muted">CO₂</span>
                <span class="text-white fw-semibold"><?= Helpers::e((string)round((float)$m['co2'], 2)) ?> kg</span>
              </div>
              <div class="d-flex justify-content-between">
                <span class="app-muted">Waste</span>
                <span class="text-white fw-semibold"><?= Helpers::e((string)round((float)$m['waste'], 2)) ?> kg</span>
              </div>
            </div>

          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

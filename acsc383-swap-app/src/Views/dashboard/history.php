<?php
declare(strict_types=1);

use App\Core\Helpers;
use App\Core\Auth;

$rows = $rows ?? [];
$uid = Auth::id() ?? 0;
?>
<div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
  <div>
    <div class="h4 mb-1">Impact History</div>
    <div class="app-muted">Completed transactions you were involved in.</div>
  </div>
  <a class="btn app-btn-soft" href="<?= Helpers::route('impact') ?>">Back to dashboard</a>
</div>

<?php if (count($rows) === 0): ?>
  <div class="app-card p-4">
    <div class="h6 mb-1">No completed history yet</div>
    <div class="app-muted">Complete a transaction to populate this page.</div>
  </div>
  <?php return; ?>
<?php endif; ?>

<div class="row g-3">
  <?php foreach ($rows as $r): ?>
    <?php
      $role = ((int)$r['seller_id'] === (int)$uid) ? 'Seller/Owner' : 'Buyer/Receiver';
      $when = (string)($r['updated_at'] ?? $r['created_at'] ?? '');
    ?>
    <div class="col-12">
      <div class="app-card p-3">
        <div class="d-flex flex-wrap justify-content-between gap-2">
          <div>
            <div class="fw-semibold text-white"><?= Helpers::e((string)$r['title']) ?></div>
            <div class="small app-muted">
              <?= Helpers::e((string)$r['transaction_type']) ?> • <?= Helpers::e($role) ?> • <?= Helpers::e($when) ?>
            </div>
          </div>
          <span class="badge bg-success-subtle text-success-emphasis">COMPLETED</span>
        </div>

        <div class="mt-3 d-flex flex-wrap gap-2">
          <span class="app-pill">Category: <?= Helpers::e((string)$r['category']) ?></span>
          <span class="app-pill">Weight: <?= Helpers::e((string)$r['weight_kg']) ?> kg</span>
          <span class="app-pill">CO₂: ~<?= Helpers::e((string)$r['impact_co2']) ?> kg</span>
          <span class="app-pill">Waste: ~<?= Helpers::e((string)$r['impact_waste']) ?> kg</span>
          <a class="btn btn-sm app-btn-soft ms-auto" href="<?= Helpers::route('item_show', ['id' => (int)$r['item_id']]) ?>">View item</a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php
declare(strict_types=1);

use App\Core\Helpers;

$rows = $rows ?? [];
?>
<div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
  <div>
    <div class="h4 mb-1">My transactions</div>
    <div class="app-muted">Track proposals, accepted deals, and completions.</div>
  </div>
</div>

<?php if (count($rows) === 0): ?>
  <div class="app-card p-4">
    <div class="h5 mb-1">No transactions yet</div>
    <div class="app-muted">Propose on an item to start.</div>
  </div>
  <?php return; ?>
<?php endif; ?>

<div class="row g-3">
  <?php foreach ($rows as $t): ?>
    <div class="col-12">
      <a class="text-decoration-none" href="<?= Helpers::route('tx_show', ['id' => (int)$t['transaction_id']]) ?>">
        <div class="app-card p-3">
          <div class="d-flex flex-wrap justify-content-between gap-2">
            <div>
              <div class="fw-semibold text-white"><?= Helpers::e((string)$t['title']) ?></div>
              <div class="small app-muted">Type: <?= Helpers::e((string)$t['transaction_type']) ?></div>
            </div>
            <span class="badge badge-soft"><?= Helpers::e((string)$t['status']) ?></span>
          </div>
        </div>
      </a>
    </div>
  <?php endforeach; ?>
</div>

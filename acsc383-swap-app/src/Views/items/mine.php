<?php
declare(strict_types=1);

use App\Core\Helpers;

$items = $items ?? [];
?>
<div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
  <div>
    <div class="h4 mb-1">My items</div>
    <div class="app-muted">Manage your listings (available items only can be edited/deleted).</div>
  </div>
  <a class="btn btn-primary app-btn-primary" href="<?= Helpers::route('item_create') ?>">List an item</a>
</div>

<?php if (count($items) === 0): ?>
  <div class="app-card p-4">
    <div class="h5 mb-1">No items yet</div>
    <div class="app-muted">Create your first listing.</div>
    <div class="mt-3">
      <a class="btn app-btn-soft" href="<?= Helpers::route('item_create') ?>">List an item</a>
    </div>
  </div>
  <?php return; ?>
<?php endif; ?>

<div class="row g-3">
  <?php foreach ($items as $it): ?>
    <div class="col-12 col-lg-6">
      <div class="app-card p-3">
        <div class="d-flex justify-content-between align-items-start gap-2">
          <div>
            <div class="fw-semibold"><?= Helpers::e((string)$it['title']) ?></div>
            <div class="small app-muted">Type: <?= Helpers::e((string)$it['transaction_type']) ?> • Status: <?= Helpers::e((string)$it['status']) ?></div>
          </div>
          <span class="badge badge-soft"><?= Helpers::e((string)$it['created_at']) ?></span>
        </div>

        <div class="small app-muted mt-2 text-truncate-2">
          <?= Helpers::e((string)$it['description']) ?>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-3">
          <span class="app-pill"><?= Helpers::e((string)$it['category']) ?></span>
          <span class="app-pill"><?= Helpers::e((string)$it['condition']) ?></span>
          <?php if ((string)$it['transaction_type'] === 'SALE'): ?>
            <span class="app-pill">€<?= Helpers::e((string)($it['price'] ?? '')) ?></span>
          <?php endif; ?>
        </div>

        <div class="mt-3 app-divider"></div>

        <div class="d-flex flex-wrap gap-2 mt-3">
          <a class="btn btn-sm app-btn-soft" href="<?= Helpers::route('item_show', ['id' => (int)$it['item_id']]) ?>">View</a>

          <?php if ((string)$it['status'] === 'AVAILABLE'): ?>
            <a class="btn btn-sm btn-primary app-btn-primary" href="<?= Helpers::route('item_edit', ['id' => (int)$it['item_id']]) ?>">Edit</a>
            <a class="btn btn-sm app-btn-soft"
               href="<?= Helpers::route('item_delete', ['id' => (int)$it['item_id']]) ?>"
               onclick="return confirm('Delete this item?')">Delete</a>
          <?php else: ?>
            <span class="app-pill">Locked (<?= Helpers::e((string)$it['status']) ?>)</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

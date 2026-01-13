<?php
declare(strict_types=1);

use App\Core\Helpers;

$items = $items ?? [];
$filters = $filters ?? ['q'=>'','category'=>'','condition'=>'','type'=>''];
$categories = $categories ?? [];
$conditions = $conditions ?? [];

function pill(string $text): string {
  return '<span class="app-pill">' . \App\Core\Helpers::e($text) . '</span>';
}

function firstImage(array $item): string {
  return \App\Core\Helpers::asset('assets/img/brand-mark.svg');
}
?>
<div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
  <div>
    <div class="h4 mb-1">Browse items</div>
    <div class="app-muted">Only available items are shown.</div>
  </div>
  <a class="btn btn-primary app-btn-primary" href="<?= Helpers::route('item_create') ?>">List an item</a>
</div>

<div class="app-card p-3 p-md-4 mb-4">
  <form class="row g-2 g-md-3 align-items-end" method="get" action="<?= Helpers::route('browse') ?>">
    <input type="hidden" name="r" value="browse">

    <div class="col-12 col-lg-5">
      <label class="form-label small app-muted">Search</label>
      <input class="form-control app-input" name="q" value="<?= Helpers::e((string)$filters['q']) ?>" placeholder="e.g., lamp, textbook, chair">
    </div>

    <div class="col-6 col-lg-2">
      <label class="form-label small app-muted">Type</label>
      <select class="form-select app-input" name="type">
        <option value="">All</option>
        <?php foreach (['SWAP','SALE','DONATION'] as $t): ?>
          <option value="<?= $t ?>" <?= ($filters['type'] === $t) ? 'selected' : '' ?>><?= $t ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-6 col-lg-2">
      <label class="form-label small app-muted">Category</label>
      <select class="form-select app-input" name="category">
        <option value="">All</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= Helpers::e($c) ?>" <?= ($filters['category'] === $c) ? 'selected' : '' ?>><?= Helpers::e($c) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-6 col-lg-2">
      <label class="form-label small app-muted">Condition</label>
      <select class="form-select app-input" name="condition">
        <option value="">All</option>
        <?php foreach ($conditions as $c): ?>
          <option value="<?= Helpers::e($c) ?>" <?= ($filters['condition'] === $c) ? 'selected' : '' ?>><?= Helpers::e($c) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-6 col-lg-1">
      <button class="btn app-btn-soft w-100">Go</button>
    </div>
  </form>
</div>

<?php if (count($items) === 0): ?>
  <div class="app-card p-4">
    <div class="h5 mb-1">No items found</div>
    <div class="app-muted">Try changing filters or list the first item.</div>
  </div>
  <?php return; ?>
<?php endif; ?>

<div class="row g-3">
  <?php foreach ($items as $it): ?>
    <div class="col-12 col-md-6 col-lg-4">
      <a class="text-decoration-none" href="<?= Helpers::route('item_show', ['id' => (int)$it['item_id']]) ?>">
        <div class="app-card p-3 h-100 card-hover">
          <div class="d-flex align-items-start justify-content-between gap-2">
            <div class="fw-semibold text-white text-truncate" style="max-width: 75%;">
              <?= Helpers::e((string)$it['title']) ?>
            </div>
            <span class="badge badge-soft">
                <?= Helpers::e((string)$it['transaction_type']) ?> • <?= Helpers::e((string)$it['status']) ?>
            </span>

          </div>

          <div class="small app-muted mt-1 text-truncate-2">
            <?= Helpers::e((string)$it['description']) ?>
          </div>

          <div class="d-flex flex-wrap gap-2 mt-3">
            <?= pill('Category: ' . (string)$it['category']) ?>
            <?= pill('Cond: ' . (string)$it['condition']) ?>
          </div>

          <div class="mt-3 app-divider"></div>

          <div class="d-flex justify-content-between align-items-center mt-3 small">
            <span class="app-muted">By <?= Helpers::e((string)$it['display_name']) ?></span>
            <?php if ((string)$it['transaction_type'] === 'SALE'): ?>
              <span class="fw-semibold text-white">€<?= Helpers::e((string)($it['price'] ?? '')) ?></span>
            <?php else: ?>
              <span class="app-muted">Available</span>
            <?php endif; ?>
          </div>
        </div>
      </a>
    </div>
  <?php endforeach; ?>
</div>

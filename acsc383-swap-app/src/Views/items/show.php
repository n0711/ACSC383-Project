<?php
declare(strict_types=1);

use App\Core\Helpers;
use App\Core\Auth;
use App\Services\ImpactService;

$item = $item ?? null;
$images = $images ?? [];
$userId = Auth::id();
$weight = (float)($item['weight_kg'] ?? 0);
[$co2, $waste] = ImpactService::calculate((string)$item['category'], $weight);

if (!$item) {
  echo "<div class='app-card p-4'>Item not found.</div>";
  return;
}

$isOwner = $userId !== null && (int)$item['owner_id'] === (int)$userId;

?>
<div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
  <div>
    <div class="h4 mb-1"><?= Helpers::e((string)$item['title']) ?></div>
    <div class="app-muted">Status: <?= Helpers::e((string)$item['status']) ?> • Type: <?= Helpers::e((string)$item['transaction_type']) ?></div>
  </div>
  <a class="btn app-btn-soft" href="<?= Helpers::route('browse') ?>">Back to browse</a>
</div>

<div class="row g-3">
  <div class="col-12 col-lg-7">
    <div class="app-card p-3 p-md-4">
      <?php if (count($images) > 0): ?>
        <div id="itemCarousel" class="carousel slide mb-3" data-bs-ride="carousel">
          <div class="carousel-inner rounded-4 overflow-hidden" style="border:1px solid rgba(255,255,255,.10);">
            <?php foreach ($images as $idx => $img): ?>
              <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                <img src="<?= Helpers::asset((string)$img['file_path']) ?>" class="d-block w-100" style="max-height:420px;object-fit:cover;">
              </div>
            <?php endforeach; ?>
          </div>
          <?php if (count($images) > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#itemCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#itemCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon"></span>
            </button>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="app-card--flat p-4 mb-3 text-center">
          <div class="app-muted">No images uploaded.</div>
        </div>
      <?php endif; ?>

      <div class="app-card--flat p-3">
        <div class="fw-semibold mb-2">Description</div>
        <div class="app-muted" style="white-space:pre-wrap;"><?= Helpers::e((string)$item['description']) ?></div>
      </div>

      <div class="d-flex flex-wrap gap-2 mt-3">
        <span class="app-pill">Category: <?= Helpers::e((string)$item['category']) ?></span>
        <span class="app-pill">Condition: <?= Helpers::e((string)$item['condition']) ?></span>
        <?php if ((string)$item['transaction_type'] === 'SALE'): ?>
          <span class="app-pill">Price: €<?= Helpers::e((string)($item['price'] ?? '')) ?></span>
        <?php endif; ?>
            <span class="app-pill">Weight: <?= Helpers::e((string)$item['weight_kg']) ?> kg</span>

      </div>
    </div>
  </div>

  <div class="col-12 col-lg-5">
    <div class="app-card p-3 p-md-4">
      <div class="d-flex justify-content-between align-items-center">
        <div class="fw-semibold">Owner</div>
        <span class="badge badge-soft"><?= Helpers::e((string)$item['display_name']) ?></span>
      </div>

      <div class="mt-3 app-card--flat p-3">
        <div class="fw-semibold mb-1">Estimated Impact (dummy)</div>
        <div class="small app-muted">Based on category coefficients × <?= Helpers::e((string)$item['weight_kg']) ?> kg.</div>
        <div class="mt-2 d-flex justify-content-between small">
          <span class="app-muted">CO₂ saved</span><span class="fw-semibold text-white">~<?= Helpers::e((string)$co2) ?> kg</span>
        </div>
        <div class="d-flex justify-content-between small">
          <span class="app-muted">Waste diverted</span><span class="fw-semibold text-white">~<?= Helpers::e((string)$waste) ?> kg</span>
        </div>
      </div>

      <?php if ($isOwner): ?>
        <div class="mt-3 d-grid gap-2">
          <a class="btn btn-primary app-btn-primary" href="<?= Helpers::route('item_edit', ['id' => (int)$item['item_id']]) ?>">Edit</a>
          <a class="btn app-btn-soft"
             href="<?= Helpers::route('item_delete', ['id' => (int)$item['item_id']]) ?>"
             onclick="return confirm('Delete this item? (Only available items can be deleted)')">Delete</a>
        </div>
      <?php else: ?>
        <div class="mt-3 d-grid gap-2">
            <?php if ((string)$item['status'] !== 'AVAILABLE'): ?>
                <button class="btn btn-secondary w-100" disabled>
                    Not available (<?= Helpers::e((string)$item['status']) ?>)
                </button>
                <?php else: ?>
                    <a class="btn btn-primary app-btn-primary"
                        href="<?= Helpers::route('tx_propose', ['item_id' => (int)$item['item_id']]) ?>"
                        onclick="return confirm('Propose this transaction? The item will be reserved.')">
                        Propose
                    </a>
            <?php endif; ?>

        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

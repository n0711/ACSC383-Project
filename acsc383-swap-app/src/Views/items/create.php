<?php
declare(strict_types=1);

use App\Core\Helpers;

$errors = $errors ?? [];
$values = $values ?? [];
$categories = $categories ?? [];
$conditions = $conditions ?? [];

function cls(array $errors, string $k): string { return isset($errors[$k]) ? 'is-invalid' : ''; }
function old(array $values, string $k): string { return (string)($values[$k] ?? ''); }
?>
<div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
  <div>
    <div class="h4 mb-1">List an item</div>
    <div class="app-muted">Add photos for better visibility.</div>
  </div>
  <a class="btn app-btn-soft" href="<?= Helpers::route('my_items') ?>">My items</a>
</div>

<div class="row justify-content-center">
  <div class="col-12 col-lg-8">
    <div class="app-card p-3 p-md-4">
      <form method="post" action="<?= Helpers::route('item_create') ?>" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">Title</label>
          <input class="form-control app-input <?= cls($errors,'title') ?>" name="title" value="<?= Helpers::e(old($values,'title')) ?>" placeholder="e.g., Desk lamp">
          <?php if (isset($errors['title'])): ?><div class="invalid-feedback"><?= Helpers::e($errors['title']) ?></div><?php endif; ?>
        </div>

        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea class="form-control app-input <?= cls($errors,'description') ?>" name="description" rows="5" placeholder="Condition, size, pickup details..."><?= Helpers::e(old($values,'description')) ?></textarea>
          <?php if (isset($errors['description'])): ?><div class="invalid-feedback"><?= Helpers::e($errors['description']) ?></div><?php endif; ?>
        </div>

        <div class="row g-3">
          <div class="col-12 col-md-4">
            <label class="form-label">Category</label>
            <select class="form-select app-input <?= cls($errors,'category') ?>" name="category">
              <option value="">Select</option>
              <?php foreach ($categories as $c): ?>
                <option value="<?= Helpers::e($c) ?>" <?= old($values,'category') === $c ? 'selected' : '' ?>><?= Helpers::e($c) ?></option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['category'])): ?><div class="invalid-feedback"><?= Helpers::e($errors['category']) ?></div><?php endif; ?>
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Condition</label>
            <select class="form-select app-input <?= cls($errors,'condition') ?>" name="condition">
              <option value="">Select</option>
              <?php foreach ($conditions as $c): ?>
                <option value="<?= Helpers::e($c) ?>" <?= old($values,'condition') === $c ? 'selected' : '' ?>><?= Helpers::e($c) ?></option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['condition'])): ?><div class="invalid-feedback"><?= Helpers::e($errors['condition']) ?></div><?php endif; ?>
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Transaction type</label>
            <select class="form-select app-input <?= cls($errors,'transaction_type') ?>" name="transaction_type" id="txType">
              <option value="">Select</option>
              <?php foreach (['SWAP','SALE','DONATION'] as $t): ?>
                <option value="<?= $t ?>" <?= old($values,'transaction_type') === $t ? 'selected' : '' ?>><?= $t ?></option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['transaction_type'])): ?><div class="invalid-feedback"><?= Helpers::e($errors['transaction_type']) ?></div><?php endif; ?>
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-12 col-md-4">
            <label class="form-label">Price (only for SALE)</label>
            <div class="input-group">
              <span class="input-group-text bg-transparent text-white-50" style="border:1px solid rgba(255,255,255,.10);">€</span>
              <input class="form-control app-input <?= cls($errors,'price') ?>" name="price" id="price" value="<?= Helpers::e(old($values,'price')) ?>" placeholder="e.g., 10.00">
              <?php if (isset($errors['price'])): ?><div class="invalid-feedback"><?= Helpers::e($errors['price']) ?></div><?php endif; ?>
            </div>
          </div>
        <div class="col-12 col-md-4">
            <label class="form-label">Weight (kg)</label>
            <input class="form-control app-input <?= cls($errors,'weight_kg') ?>"
                    name="weight_kg"
                    value="<?= Helpers::e(old($values,'weight_kg')) ?>"
                    placeholder="e.g., 1.50">
            <?php if (isset($errors['weight_kg'])): ?>
            <div class="invalid-feedback"><?= Helpers::e($errors['weight_kg']) ?></div>
                <?php endif; ?>
            <div class="small app-muted mt-1">Used later for CO₂ / waste calculations.</div>
        </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Images (JPEG/PNG/WEBP)</label>
            <input class="form-control app-input" type="file" name="images[]" multiple accept="image/*">
            <div class="small app-muted mt-1">You can upload multiple images. Max size: <?= (int)MAX_UPLOAD_MB ?>MB each.</div>
          </div>
        </div>

        <div class="d-grid mt-4">
          <button class="btn btn-primary app-btn-primary btn-lg">Publish listing</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(function(){
  function togglePrice(){
    const t = $("#txType").val();
    if (t === "SALE") {
      $("#price").prop("disabled", false);
    } else {
      $("#price").prop("disabled", true).val("");
    }
  }
  $("#txType").on("change", togglePrice);
  togglePrice();
});
</script>

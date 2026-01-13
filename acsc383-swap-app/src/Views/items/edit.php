<?php
declare(strict_types=1);

use App\Core\Helpers;

$errors = $errors ?? [];
$values = $values ?? [];
$item = $item ?? [];
$images = $images ?? [];
$categories = $categories ?? [];
$conditions = $conditions ?? [];

function cls(array $errors, string $k): string { return isset($errors[$k]) ? 'is-invalid' : ''; }
function old(array $values, string $k): string { return (string)($values[$k] ?? ''); }
?>
<div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
  <div>
    <div class="h4 mb-1">Edit item</div>
    <div class="app-muted">Only available items can be edited.</div>
  </div>
  <a class="btn app-btn-soft" href="<?= Helpers::route('item_show', ['id' => (int)$item['item_id']]) ?>">Back to item</a>
</div>

<div class="row justify-content-center">
  <div class="col-12 col-lg-8">
    <div class="app-card p-3 p-md-4">
      <form method="post" action="<?= Helpers::route('item_edit', ['id' => (int)$item['item_id']]) ?>" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">Title</label>
          <input class="form-control app-input <?= cls($errors,'title') ?>" name="title" value="<?= Helpers::e(old($values,'title')) ?>">
          <?php if (isset($errors['title'])): ?><div class="invalid-feedback"><?= Helpers::e($errors['title']) ?></div><?php endif; ?>
        </div>

        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea class="form-control app-input <?= cls($errors,'description') ?>" name="description" rows="5"><?= Helpers::e(old($values,'description')) ?></textarea>
          <?php if (isset($errors['description'])): ?><div class="invalid-feedback"><?= Helpers::e($errors['description']) ?></div><?php endif; ?>
        </div>

        <div class="row g-3">
          <div class="col-12 col-md-4">
            <label class="form-label">Category</label>
            <select class="form-select app-input <?= cls($errors,'category') ?>" name="category">
              <?php foreach ($categories as $c): ?>
                <option value="<?= Helpers::e($c) ?>" <?= old($values,'category') === $c ? 'selected' : '' ?>><?= Helpers::e($c) ?></option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['category'])): ?><div class="invalid-feedback"><?= Helpers::e($errors['category']) ?></div><?php endif; ?>
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Condition</label>
            <select class="form-select app-input <?= cls($errors,'condition') ?>" name="condition">
              <?php foreach ($conditions as $c): ?>
                <option value="<?= Helpers::e($c) ?>" <?= old($values,'condition') === $c ? 'selected' : '' ?>><?= Helpers::e($c) ?></option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['condition'])): ?><div class="invalid-feedback"><?= Helpers::e($errors['condition']) ?></div><?php endif; ?>
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Transaction type</label>
            <select class="form-select app-input <?= cls($errors,'transaction_type') ?>" name="transaction_type" id="txType">
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
              <input class="form-control app-input <?= cls($errors,'price') ?>" name="price" id="price" value="<?= Helpers::e(old($values,'price')) ?>">
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
                <div class="invalid-feedback"><?= Helpers::e(old($values,'weight_kg')) ?></div>
            <?php endif; ?>
            <div class="small app-muted mt-1">Used later for CO₂ / waste calculations.</div>
        </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Add new images</label>
            <input class="form-control app-input" type="file" name="images[]" multiple accept="image/*">
            <div class="small app-muted mt-1">Existing images remain. Upload more to add.</div>
          </div>
        </div>

        <?php if (count($images) > 0): ?>
          <div class="mt-4 app-divider"></div>
          <div class="mt-3 fw-semibold">Existing images</div>
          <div class="row g-2 mt-1">
            <?php foreach ($images as $img): ?>
              <div class="col-6 col-md-4">
                <div class="app-card--flat p-2">
                  <img src="<?= Helpers::asset((string)$img['file_path']) ?>" class="w-100 rounded-3" style="aspect-ratio: 4/3; object-fit: cover;">
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div class="d-grid mt-4">
          <button class="btn btn-primary app-btn-primary btn-lg">Save changes</button>
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

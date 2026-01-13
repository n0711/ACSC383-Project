<?php
declare(strict_types=1);

use App\Core\Helpers;
?>
<footer class="app-footer mt-4">
  <div class="container app-container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
      <div>
        <div class="fw-semibold text-white"><?= Helpers::e(APP_NAME) ?></div>
        <div class="small app-muted">Swap • Sell • Donate — track CO₂ & waste saved.</div>
      </div>
      <div class="small app-muted">
        © <?= date('Y') ?> • Built for ACSC383
      </div>
    </div>
  </div>
</footer>

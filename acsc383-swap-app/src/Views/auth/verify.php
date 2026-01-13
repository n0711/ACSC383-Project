<?php
declare(strict_types=1);

use App\Core\Helpers;

$token = (string)($token ?? '');
$ok = $ok ?? null;
?>
<div class="row justify-content-center">
  <div class="col-12 col-lg-6">
    <div class="app-card p-4">
      <div class="d-flex align-items-start justify-content-between gap-3">
        <div>
          <div class="h4 mb-1">Verify account (Demo)</div>
          <div class="app-muted">This simulates email verification for the project.</div>
        </div>
        <a class="btn app-btn-soft" href="<?= Helpers::route('login') ?>">Sign in</a>
      </div>

      <div class="mt-4 app-card--flat p-3">
        <div class="small app-muted">Token</div>
        <div class="fw-semibold" style="word-break: break-all;"><?= Helpers::e($token) ?></div>
      </div>

      <div class="mt-3">
        <?php if ($ok === true): ?>
          <div class="alert alert-success">Verified successfully.</div>
        <?php elseif ($ok === false): ?>
          <div class="alert alert-danger">Invalid token (demo).</div>
        <?php else: ?>
          <div class="alert alert-warning">No token provided.</div>
        <?php endif; ?>
      </div>

      <div class="d-grid mt-3">
        <a class="btn btn-primary app-btn-primary btn-lg" href="<?= Helpers::route('login') ?>">Continue to Sign in</a>
      </div>
    </div>
  </div>
</div>

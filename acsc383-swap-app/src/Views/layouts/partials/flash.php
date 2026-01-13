<?php
declare(strict_types=1);

use App\Core\Helpers;

$messages = Helpers::consumeFlash();
if (!$messages) return;

$map = [
  'success' => 'alert-success',
  'danger'  => 'alert-danger',
  'warning' => 'alert-warning',
  'info'    => 'alert-info',
];
?>
<div class="mb-3">
  <?php foreach ($messages as $m): ?>
    <?php $type = (string)($m['type'] ?? 'info'); ?>
    <div class="alert <?= $map[$type] ?? 'alert-info' ?> mb-2">
      <?= Helpers::e((string)($m['message'] ?? '')) ?>
    </div>
  <?php endforeach; ?>
</div>

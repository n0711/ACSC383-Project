<?php
declare(strict_types=1);

use App\Core\Helpers;

$tx = $tx ?? null;
$uid = (int)($uid ?? 0);

if (!$tx) { echo "<div class='app-card p-4'>Not found.</div>"; return; }

$isSeller = (int)$tx['seller_id'] === $uid;
$isBuyer  = (int)$tx['buyer_id'] === $uid;

$status = (string)$tx['status'];
$type = (string)$tx['transaction_type'];

$canAccept = $isSeller && $status === 'PENDING';
$canDecline = $isSeller && $status === 'PENDING';
$canCancel = ($isSeller || $isBuyer) && in_array($status, ['PENDING','ACCEPTED'], true);
$canComplete = ($isSeller || $isBuyer) && $status === 'ACCEPTED';
$needsProof = in_array($type, ['SALE','DONATION'], true);
?>
<div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
  <div>
    <div class="h4 mb-1">Transaction #<?= (int)$tx['transaction_id'] ?></div>
    <div class="app-muted"><?= Helpers::e((string)$tx['title']) ?> • <?= Helpers::e($type) ?></div>
  </div>
  <a class="btn app-btn-soft" href="<?= Helpers::route('tx_mine') ?>">Back</a>
</div>

<div class="row g-3">
  <div class="col-12 col-lg-7">
    <div class="app-card p-3 p-md-4">
      <div class="d-flex justify-content-between align-items-center">
        <div class="fw-semibold">Status</div>
        <span class="badge badge-soft"><?= Helpers::e($status) ?></span>
      </div>

      <div class="mt-3 app-card--flat p-3">
        <div class="small app-muted">Role</div>
        <div class="fw-semibold"><?= $isSeller ? 'Seller/Owner' : 'Buyer/Receiver' ?></div>
      </div>

      <?php if (!empty($tx['buyer_proof_path'])): ?>
        <div class="mt-3 app-card--flat p-3">
          <div class="fw-semibold mb-2">Proof photo</div>
          <img src="<?= Helpers::asset((string)$tx['buyer_proof_path']) ?>" class="w-100 rounded-4" style="max-height:420px;object-fit:cover;">
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-12 col-lg-5">
    <div class="app-card p-3 p-md-4">
      <div class="fw-semibold mb-2">Actions</div>

      <div class="d-grid gap-2">
        <?php if ($canAccept): ?>
          <a class="btn btn-primary app-btn-primary" href="<?= Helpers::route('tx_action', ['id'=>(int)$tx['transaction_id'],'act'=>'accept']) ?>">Accept</a>
        <?php endif; ?>

        <?php if ($canDecline): ?>
          <a class="btn app-btn-soft" href="<?= Helpers::route('tx_action', ['id'=>(int)$tx['transaction_id'],'act'=>'decline']) ?>"
             onclick="return confirm('Decline this proposal?')">Decline</a>
        <?php endif; ?>

        <?php if ($canCancel): ?>
          <a class="btn app-btn-soft" href="<?= Helpers::route('tx_action', ['id'=>(int)$tx['transaction_id'],'act'=>'cancel']) ?>"
             onclick="return confirm('Cancel this transaction?')">Cancel</a>
        <?php endif; ?>

        <?php if ($canComplete): ?>
          <form method="post" action="<?= Helpers::route('tx_complete', ['id'=>(int)$tx['transaction_id']]) ?>" enctype="multipart/form-data">
            <button class="btn btn-primary app-btn-primary w-100">Mark Completed</button>
          </form>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

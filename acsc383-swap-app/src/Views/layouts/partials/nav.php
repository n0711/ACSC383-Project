<?php
declare(strict_types=1);

use App\Core\Helpers;
use App\Core\Auth;

$user = Auth::user();
?>
<nav class="navbar navbar-expand-lg app-nav sticky-top">
  <div class="container app-container">
    <a class="navbar-brand d-flex align-items-center gap-2 text-white"
       href="<?= Helpers::route('landing') ?>">
      <span class="d-inline-flex align-items-center justify-content-center rounded-3"
            style="width:36px;height:36px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.04);">
        <img src="<?= Helpers::asset('assets/img/brand-mark.svg') ?>" alt="brand" style="width:20px;height:20px;">
      </span>
      <span class="fw-semibold"><?= Helpers::e(APP_NAME) ?></span>
      
    </a>

    <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#appNav">
      <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
    </button>

    <div class="collapse navbar-collapse" id="appNav">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
        <li class="nav-item"><a class="nav-link text-white-50" href="<?= Helpers::route('browse') ?>">Browse</a></li>

        <?php if ($user): ?>
          
          <li class="nav-item"><a class="nav-link text-white-50" href="<?= Helpers::route('my_items') ?>">My Items</a></li>
          <li class="nav-item"><a class="nav-link text-white-50" href="<?= Helpers::route('tx_mine') ?>">Transactions</a></li>
          <li class="nav-item"><a class="nav-link text-white-50" href="<?= \App\Core\Helpers::route('impact') ?>">Impact</a></li>
          
          <li class="nav-item">
            <span><a class="btn btn-sm app-btn-soft" href="<?= Helpers::route('logout') ?>">Logout</a></span>
            <span class="app-pill"><?= Helpers::e((string)$user['display_name']) ?></span>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="btn btn-sm app-btn-soft" href="<?= Helpers::route('login') ?>">Sign in</a></li>
          <li class="nav-item"><a class="btn btn-sm btn-primary app-btn-primary" href="<?= Helpers::route('register') ?>">Create account</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

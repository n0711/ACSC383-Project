<?php
declare(strict_types=1);

use App\Core\Helpers;

$errors = $errors ?? [];
$values = $values ?? ['email' => ''];

function fieldClass(array $errors, string $key): string {
  return isset($errors[$key]) ? 'is-invalid' : '';
}
?>
<div class="row justify-content-center">
  <div class="col-12 col-lg-6">
    <div class="app-card p-4">
      <div class="d-flex align-items-start justify-content-between gap-3">
        <div>
          <div class="h4 mb-1">Sign in</div>
          <div class="app-muted">Use your verified university account.</div>
        </div>
        <a class="btn app-btn-soft" href="<?= Helpers::route('register') ?>">Create account</a>
      </div>

      <div class="mt-4">
        <form method="post" action="<?= Helpers::route('login') ?>">
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input class="form-control app-input <?= fieldClass($errors,'email') ?>"
                   name="email"
                   value="<?= Helpers::e((string)($values['email'] ?? '')) ?>"
                   placeholder="name@university.edu" />
            <?php if (isset($errors['email'])): ?>
              <div class="invalid-feedback"><?= Helpers::e($errors['email']) ?></div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control app-input <?= fieldClass($errors,'password') ?>"
                   name="password" placeholder="Your password" />
            <?php if (isset($errors['password'])): ?>
              <div class="invalid-feedback"><?= Helpers::e($errors['password']) ?></div>
            <?php endif; ?>
          </div>

          <div class="d-grid mt-4">
            <button class="btn btn-primary app-btn-primary btn-lg">Sign in</button>
          </div>

          <div class="mt-3 small app-muted">
            If your account is not verified, register again and use the demo verification link.
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

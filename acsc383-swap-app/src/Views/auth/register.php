<?php
declare(strict_types=1);

use App\Core\Helpers;

$errors = $errors ?? [];
$values = $values ?? ['display_name' => '', 'email' => ''];
$verifyLink = $verifyLink ?? null;

function fieldClass(array $errors, string $key): string {
  return isset($errors[$key]) ? 'is-invalid' : '';
}
?>
<div class="row justify-content-center">
  <div class="col-12 col-lg-6">
    <div class="app-card p-4">
      <div class="d-flex align-items-start justify-content-between gap-3">
        <div>
          <div class="h4 mb-1">Create account</div>
          <div class="app-muted">Demo verification (we’ll show the link after signup).</div>
        </div>
        <a class="btn app-btn-soft" href="<?= Helpers::route('login') ?>">Sign in</a>
      </div>

      <div class="mt-4">
        <form method="post" action="<?= Helpers::route('register') ?>">
          <div class="mb-3">
            <label class="form-label">Display name</label>
            <input class="form-control app-input <?= fieldClass($errors,'display_name') ?>"
                   name="display_name"
                   value="<?= Helpers::e((string)($values['display_name'] ?? '')) ?>"
                   placeholder="e.g., Maria P." />
            <?php if (isset($errors['display_name'])): ?>
              <div class="invalid-feedback"><?= Helpers::e($errors['display_name']) ?></div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label class="form-label">University email</label>
            <input class="form-control app-input <?= fieldClass($errors,'email') ?>"
                   name="email"
                   value="<?= Helpers::e((string)($values['email'] ?? '')) ?>"
                   placeholder="name@university.edu" />
            <?php if (isset($errors['email'])): ?>
              <div class="invalid-feedback"><?= Helpers::e($errors['email']) ?></div>
            <?php endif; ?>
          </div>

          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Password</label>
              <input type="password" class="form-control app-input <?= fieldClass($errors,'password') ?>"
                     name="password" placeholder="Min 6 chars" />
              <?php if (isset($errors['password'])): ?>
                <div class="invalid-feedback"><?= Helpers::e($errors['password']) ?></div>
              <?php endif; ?>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Confirm password</label>
              <input type="password" class="form-control app-input <?= fieldClass($errors,'password2') ?>"
                     name="password2" placeholder="Repeat password" />
              <?php if (isset($errors['password2'])): ?>
                <div class="invalid-feedback"><?= Helpers::e($errors['password2']) ?></div>
              <?php endif; ?>
            </div>
          </div>

          <div class="d-grid mt-4">
            <button class="btn btn-primary app-btn-primary btn-lg">Create account</button>
          </div>
        </form>
      </div>

      <?php if ($verifyLink): ?>
        <div class="mt-4 app-divider"></div>
        <div class="mt-3">
          <div class="fw-semibold mb-1">Demo verification link</div>
          <div class="small app-muted mb-2">Click to verify your account (instead of sending email).</div>
          <a class="btn app-btn-soft" href="<?= Helpers::e($verifyLink) ?>">Verify now</a>
          <div class="small app-muted mt-2">
            Or copy: <code class="text-white"><?= Helpers::e($verifyLink) ?></code>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

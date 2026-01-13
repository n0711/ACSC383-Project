<?php
declare(strict_types=1);

use App\Core\Helpers;

$title = $title ?? APP_NAME;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= Helpers::e((string)$title) ?></title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= Helpers::asset('assets/css/app.css') ?>" rel="stylesheet">
</head>
<body>

<?php require __DIR__ . '/partials/nav.php'; ?>

<main class="container app-container py-4">
  <?php require __DIR__ . '/partials/flash.php'; ?>
  <?= $content ?? '' ?>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= Helpers::asset('assets/js/app.js') ?>"></script>
</body>
</html>

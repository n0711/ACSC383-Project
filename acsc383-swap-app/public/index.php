<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

use App\Core\App;
use App\Core\Request;

$app = App::getInstance();
$request = Request::fromGlobals();

$response = $app->handle($request);
$response->send();

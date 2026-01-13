<?php
declare(strict_types=1);

use App\Core\Env;

Env::load(__DIR__ . '/../.env');

define('APP_NAME', Env::get('APP_NAME', 'Uni Swap'));
define('APP_URL', Env::get('APP_URL', 'http://localhost'));

define('DB_HOST', Env::get('DB_HOST', '127.0.0.1'));
define('DB_PORT', Env::get('DB_PORT', '3306'));
define('DB_NAME', Env::get('DB_NAME', 'acsc383_swap_app'));
define('DB_USER', Env::get('DB_USER', 'root'));
define('DB_PASS', Env::get('DB_PASS', ''));

define('UPLOAD_DIR', Env::get('UPLOAD_DIR', 'uploads'));
define('MAX_UPLOAD_MB', (int) Env::get('MAX_UPLOAD_MB', '5'));

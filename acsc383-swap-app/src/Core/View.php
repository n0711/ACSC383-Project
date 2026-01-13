<?php
declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $view, array $data = []): string
    {
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        $layout = __DIR__ . '/../Views/layouts/main.php';

        if (!is_file($viewFile)) {
            return "<div class='alert alert-danger'>View not found: {$view}</div>";
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = ob_get_clean() ?: '';

        ob_start();
        require $layout;
        return ob_get_clean() ?: '';
    }
}

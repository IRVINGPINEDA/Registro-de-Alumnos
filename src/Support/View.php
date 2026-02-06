<?php
declare(strict_types=1);

namespace App\Support;

final class View
{
    /**
     * @param array<string,mixed> $data
     */
    public static function render(string $view, array $data = []): string
    {
        $viewsDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Views';
        $viewFile = $viewsDir . DIRECTORY_SEPARATOR . $view . '.php';
        if (!is_file($viewFile)) {
            throw new \RuntimeException('View not found: ' . $viewFile);
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $viewFile;
        return ob_get_clean() ?: '';
    }
}


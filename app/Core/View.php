<?php
namespace App\Core;

/**
 * Plain-PHP template renderer with optional layout wrapping.
 */
class View
{
    public static function render(string $template, array $data = [], ?string $layout = 'layouts/main'): string
    {
        $content = self::capture($template, $data);
        if ($layout === null) {
            return $content;
        }
        return self::capture($layout, array_merge($data, ['content' => $content]));
    }

    /** Internal names are prefixed with "__" so view variables (e.g. $data, $file) never collide. */
    private static function capture(string $__template, array $__vars): string
    {
        $__file = VIEW_PATH . '/' . $__template . '.php';
        if (!is_file($__file)) {
            throw new \RuntimeException("View not found: $__template");
        }
        extract($__vars, EXTR_SKIP);
        ob_start();
        try {
            include $__file;
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        return (string) ob_get_clean();
    }
}

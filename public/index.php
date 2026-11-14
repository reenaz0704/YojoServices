<?php
/**
 * Front controller — the only PHP entry point inside the web root.
 */
require dirname(__DIR__) . '/app/Core/bootstrap.php';

$router = new App\Core\Router();
require BASE_PATH . '/routes/web.php';
require BASE_PATH . '/routes/api.php';

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', current_path());

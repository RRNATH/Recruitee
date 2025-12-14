<?php
/**
 * Normalize API path
 * Removes the base path and trailing slashes
 *
 * @param string $requestUri  The full request URI ($_SERVER['REQUEST_URI'])
 * @param string $basePath    The base path of your API (e.g., /api-storage)
 * @return string             Normalized path
 */
function normalizePath(string $requestUri, string $basePath = '/api-storage'): string
{
    // Load config if basePath not provided
    if ($basePath === null) {
        $config = require __DIR__ . '/../../config.php';
        $basePath = $config['base_path'] ?? '';
    }

    $path = parse_url($requestUri, PHP_URL_PATH);

    // Remove base path if present
    if (strpos($path, $basePath) === 0) {
        $path = substr($path, strlen($basePath));
    }

    // Remove trailing slash
    $path = rtrim($path, '/');

    // Ensure path always starts with /
    if ($path === '') {
        $path = '/';
    }

    return $path;
}

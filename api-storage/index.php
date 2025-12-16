<?php


// --------------------
// CORS (WHITELISTED)
/// --------------------
$allowedOrigins = [
    'https://mk.nl',
    'https://datainbeeld.com',
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Vary: Origin");
}

header("Access-Control-Allow-Methods: GET, POST, PATCH, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/vendor/firebase/php-jwt/src/JWT.php';
require_once __DIR__ . '/vendor/firebase/php-jwt/src/Key.php';
require_once __DIR__ . '/vendor/firebase/php-jwt/src/ExpiredException.php';
require_once __DIR__ . '/vendor/firebase/php-jwt/src/SignatureInvalidException.php';
require_once __DIR__ . '/vendor/firebase/php-jwt/src/BeforeValidException.php';


require_once __DIR__ . '/api/utils/path.php';  // <-- Add this line


// Normalize the path using the utility
$path = normalizePath($_SERVER['REQUEST_URI']);
$method = $_SERVER['REQUEST_METHOD'];

// Switch on true to use conditions in cases
switch (true) {
    case $path === '/token' && $method === 'POST':
        require __DIR__ . '/api/token.php';
        break;

    case $path === '/candidates/create' && $method === 'POST':
        
        require __DIR__ . '/api/candidates.php';
        break;

    case preg_match('#^/candidates/\d+$#', $path) === 1 && $method === 'GET':
        require __DIR__ . '/api/candidates.php';
        break;

    case preg_match('#^/candidates/\d+/update_cv$#', $path) === 1 && in_array($method, ['POST', 'PATCH'], true):
        require __DIR__ . '/api/updateCV.php';
        break;

    case preg_match('#^/custom_fields/candidates/\d+/fields$#', $path) === 1 && $method === 'POST':
        require __DIR__ . '/api/customFields.php';
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}

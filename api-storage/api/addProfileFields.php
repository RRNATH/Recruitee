<?php
require_once __DIR__ . '/middleware/auth.php';
require_once __DIR__ . '/services/recruiteeService.php';
require_once __DIR__ . '/utils/path.php';

// Authenticate
$user = authenticate();

// Load config and service
$config = require __DIR__ . '/../config.php';
$service = new RecruiteeService($config);

// Normalize path and method
$path = normalizePath($_SERVER['REQUEST_URI']);
$method = $_SERVER['REQUEST_METHOD'];

// --- POST /custom_fields/candidates/{id}/fields ---
if ($method === 'POST' && preg_match('#^/custom_fields/candidates/(\d+)/fields$#', $path, $matches)) {
    $candidateId = $matches[1];
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['fields'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing fields array in request body']);
        exit;
    }

    $response = $service->post("custom_fields/candidates/{$candidateId}/fields", $input);
    http_response_code($response['status']);
    echo $response['body'];
    exit;
}

// --- No route matched ---
http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);
exit;

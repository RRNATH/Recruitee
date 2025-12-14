<?php
require_once __DIR__ . '/middleware/auth.php';
require_once __DIR__ . '/services/recruiteeService.php';
require_once __DIR__ . '/utils/path.php';

// Authenticate user
$user = authenticate();

// Load config and service
$config = require __DIR__ . '/../config.php';
$service = new RecruiteeService($config);

// Normalize path and method
$path = normalizePath($_SERVER['REQUEST_URI']);
$method = $_SERVER['REQUEST_METHOD'];

// --- POST /candidates/create ---
if ($method === 'POST' && $path === '/candidates/create') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['candidate'], $input['offer_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON or missing fields: candidate, offer_id']);
        exit;
    }

    // Validate required candidate fields
    $candidate = $input['candidate'];
    $requiredFields = ['name', 'emails'];
    foreach ($requiredFields as $field) {
        if (empty($candidate[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Candidate field '$field' is required"]);
            exit;
        }
    }

    $response = $service->post('candidates', $input);
    http_response_code($response['status']);
    echo $response['body'];
    exit;
}

// --- GET /candidates/{id} ---
if ($method === 'GET' && preg_match('#^/candidates/(\d+)$#', $path, $matches)) {
    $candidateId = $matches[1];
    $response = $service->get("candidates/{$candidateId}");
    http_response_code($response['status']);
    echo $response['body'];
    exit;
}

// --- No route matched ---
http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);
exit;

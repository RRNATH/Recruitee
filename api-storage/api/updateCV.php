<?php
require_once __DIR__ . '/middleware/auth.php';
require_once __DIR__ . '/services/recruiteeService.php';
require_once __DIR__ . '/utils/path.php';
require_once __DIR__ . '/utils/recruiteeToken.php';

// Authenticate
$user = authenticate();

// Load config
$config = require __DIR__ . '/../config.php';

// Normalize path and method
$path = normalizePath($_SERVER['REQUEST_URI']);
$method = $_SERVER['REQUEST_METHOD'];

// --- PATCH /candidates/{id}/update_cv ---
if (in_array($method, ['POST', 'PATCH'], true)
    && preg_match('#^/candidates/(\d+)/update_cv$#', $path, $matches)
) {
    $candidateId = $matches[1];

    // Require page_id query param
    if (empty($_GET['page_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Query parameter page_id is required']);
        exit;
    }

    $pageId = (string) $_GET['page_id'];
    $token  = getRecruiteeToken($config, $pageId);

    if (!$token) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid page_id']);
        exit;
    }

    $service = new RecruiteeService($config, $token);

    // Expecting form-data: candidate[cv]
    if (!isset($_FILES['candidate']['tmp_name']['cv'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing candidate[cv] file']);
        exit;
    }

    $fileTmpPath = $_FILES['candidate']['tmp_name']['cv'];
    $fileName    = $_FILES['candidate']['name']['cv'];
    $fileType    = $_FILES['candidate']['type']['cv'];

    $multipartData = [
        'candidate[cv]' => new CURLFile($fileTmpPath, $fileType, $fileName)
    ];

    $response = $service->patchMultipart(
        "candidates/{$candidateId}/update_cv",
        $multipartData
    );

    http_response_code($response['status']);
    echo $response['body'];
    exit;
}

// --- No route matched ---
http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);
exit;

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

// --- PATCH /candidates/{id}/update_cv ---
if (in_array($method, ['POST', 'PATCH'], true) && preg_match('#^/candidates/(\d+)/update_cv$#', $path, $matches)) {
    $candidateId = $matches[1];

    // Expecting form-data: candidate[cv]
    if (
        !isset($_FILES['candidate']) ||
        !isset($_FILES['candidate']['tmp_name']['cv'])
    ) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing candidate[cv] file']);
        exit;
    }

    $fileTmpPath = $_FILES['candidate']['tmp_name']['cv'];
    $fileName    = $_FILES['candidate']['name']['cv'];
    $fileType    = $_FILES['candidate']['type']['cv'];

    // Prepare multipart data for Recruitee
    $multipartData = [
        'candidate[cv]' => new CURLFile(
            $fileTmpPath,
            $fileType,
            $fileName
        )
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

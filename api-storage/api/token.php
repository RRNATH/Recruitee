<?php

use Firebase\JWT\JWT;

$config = require __DIR__ . '/../config.php';

header('Content-Type: application/json');

$issuedAt = time();
$expiration = $issuedAt + $config['jwt_ttl'];

$payload = [
    'iss' => $config['jwt_issuer'],
    'iat' => $issuedAt,
    'exp' => $expiration
];

$jwt = JWT::encode($payload, $config['jwt_secret']);

echo json_encode([
    'token' => $jwt,
    'expires_in' => $config['jwt_ttl']
]);
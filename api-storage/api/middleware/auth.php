<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

/**
 * Validates Bearer JWT token
 * @return object decoded token
 */
function authenticate()
{
    header('Content-Type: application/json');

    $config = require __DIR__ . '/../../config.php';

    // ---- Read Authorization Header ----
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Missing Authorization header']);
        exit;
    }

    if (!preg_match('/Bearer\s+(.*)$/i', $headers['Authorization'], $matches)) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid Authorization header']);
        exit;
    }

    $jwt = $matches[1];

    // ---- Decode JWT (PHP 7.4 compatible) ----
    try {
        return JWT::decode($jwt, $config['jwt_secret'], ['HS256']);
    } catch (ExpiredException $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Token expired']);
    exit;
    } catch (SignatureInvalidException $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token signature']);
        exit;
    } catch (BeforeValidException $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Token not valid yet']);
        exit;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token']);
        exit;
    }
}

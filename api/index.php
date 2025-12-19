<?php
/**
 * API Router - Central entry point (OPTIONAL)
 * 
 * This file is optional since your API uses direct endpoints.
 * It can be useful for routing and middleware in the future.
 */

header('Content-Type: application/json');

// Simple health check endpoint
if ($_SERVER['REQUEST_URI'] === '/api/' || $_SERVER['REQUEST_URI'] === '/api/index.php') {
    http_response_code(200);
    echo json_encode([
        'status' => 200,
        'message' => 'JLC Ventas API is running',
        'version' => '1.0.0',
        'endpoints' => [
            '/api/auth/login.php',
            '/api/sales/create.php',
            '/api/sales/list.php',
            '/api/products/list.php',
            '/api/uploads/upload.php',
        ]
    ]);
    exit;
}

// For all other requests, return 404
http_response_code(404);
echo json_encode([
    'status' => 404,
    'message' => 'Endpoint not found. Please use specific API endpoints.'
]);

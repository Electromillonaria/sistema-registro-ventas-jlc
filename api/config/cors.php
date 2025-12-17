<?php
// Evitar duplicación si se incluye múltiples veces
if (defined('CORS_APPLIED')) return;
define('CORS_APPLIED', true);

// Limpiar headers previos por seguridad
header_remove("Access-Control-Allow-Origin");
header_remove("Access-Control-Allow-Methods");
header_remove("Access-Control-Allow-Headers");

// Aplicar Headers CORS Permisivos (Dev)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
// Lista exhaustiva de headers permitidos para evitar bloqueos
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Origin, Accept");

// Manejo inmediato de Preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(["status" => 200, "message" => "CORS Preflight OK"]);
    exit;
}
